<?php

namespace App\Http\Controllers;

use App\Models\{Order, Company, User};
use App\Mail\{CustomerOrderConfirmation};
use App\Mail\{SalesNotification};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Http, Log, Mail, DB, Hash};
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Tarifs annuels (engagement 12 mois) en FCFA.
     * Le frontend n'envoie PLUS le montant : le serveur le recalcule
     * à partir du plan pour éviter toute falsification.
     */
    private const PRICES = [
        'SOLUTCLOUD START'    => 70800,
        'SOLUTCLOUD BUSINESS' => 118800,
    ];

    /**
     * 1. GÉNÉRATION DU LIEN DE PAIEMENT (API PUBLIC)
     * Point d'entrée pour le site vitrine via fetch.
     */
    public function createCheckout(Request $request)
    {
        $data = $request->validate([
            'fullname'     => 'required|string|max:255',
            'email'        => 'required|email',
            'company_name' => 'required|string|max:255',
            'phone'        => 'required|string|max:30',
            'plan'         => 'required|string',
        ]);

        // Sécurité : le montant est calculé côté serveur, jamais lu depuis le frontend
        $normalizedPlan = strtoupper(trim((string) $data['plan']));
        $amount = self::PRICES[$normalizedPlan] ?? null;

        if (!$amount) {
            Log::warning("MONEROO - Plan invalide reçu : " . $normalizedPlan);
            return response()->json(['error' => 'Formule invalide. Contactez support@i-solutions.ci.'], 422);
        }

        try {
            // LOGIQUE SENIOR : Séparation du nom complet pour l'API Moneroo
            $nameParts = explode(' ', trim($data['fullname']), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? $nameParts[0]; // Sécurité si un seul mot est saisi

            $baseUrl = config('services.moneroo.base_url', 'https://sandbox.moneroo.io');
            $secret  = config('services.moneroo.secret');

            // Appel à l'API Moneroo
            $response = Http::withToken($secret)
                ->post($baseUrl . '/v1/payments/initialize', [
                    'amount'      => $amount,
                    'currency'    => 'XOF',
                    'customer'    => [
                        'first_name' => $firstName,
                        'last_name'  => $lastName,
                        'email'      => $data['email'],
                        'phone'      => $data['phone'],
                    ],
                    'description' => "Activation SOLUTCLOUD - " . $normalizedPlan,
                    'return_url'  => 'https://www.solutcloud.com/merci.html',
                    'cancel_url'  => 'https://www.solutcloud.com/tarifs.html',
                    'metadata'    => [
                        'company_name' => $data['company_name'],
                        'plan'         => $normalizedPlan
                    ]
                ]);

            $result = $response->json();

            if ($response->successful() && isset($result['data']['checkout_url'])) {
                // On archive la demande de paiement avec le transaction_id de Moneroo
                Order::create([
                    'transaction_id' => $result['data']['id'],
                    'company_name'   => $data['company_name'],
                    'customer_name'  => $data['fullname'],
                    'customer_email' => $data['email'],
                    'customer_phone' => $data['phone'],
                    'plan'           => $normalizedPlan,
                    'amount'         => $amount,
                    'status'         => 'pending'
                ]);

                return response()->json([
                    'status'       => 'success',
                    'checkout_url' => $result['data']['checkout_url']
                ]);
            }

            throw new \Exception($result['message'] ?? 'Erreur API Moneroo');

        } catch (\Exception $e) {
            Log::error("CRITICAL - ÉCHEC INITIALISATION MONEROO : " . $e->getMessage());
            return response()->json(['error' => 'Une erreur technique empêche le paiement. Contactez support@i-solutions.ci'], 500);
        }
    }

    /**
     * 2. WEBHOOK : AUTOMATISATION POST-PAIEMENT
     * Cette méthode est appelée par les serveurs de Moneroo en arrière-plan.
     */
    public function handleWebhook(Request $request)
    {
        // Sécurité : Vérification de la signature (Secret Moneroo)
        $signature = $request->header('moneroo-signature');
        $webhookSecret = config('services.moneroo.webhook_secret');

        if (!$signature || !$webhookSecret || $signature !== $webhookSecret) {
            Log::warning("ATTENTION - Webhook Moneroo : Signature invalide ou absente.");
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        if (isset($payload['event']) && $payload['event'] === 'payment.success') {

            $transactionId = $payload['data']['id'] ?? null;
            if (!$transactionId) {
                Log::warning("WEBHOOK MONEROO - transaction_id manquant dans le payload");
                return response()->json(['error' => 'Invalid payload'], 400);
            }

            $order = Order::where('transaction_id', $transactionId)->first();

            // Sécurité Senior : On ne traite que les commandes existantes et non encore validées
            if ($order && $order->status === 'pending') {

                DB::transaction(function () use ($order) {

                    // A. Validation de la commande
                    $order->update(['status' => 'completed']);

                    // B. Nettoyage du nom de forfait (ex: "SOLUTCLOUD START" -> "start")
                    $cleanPackage = trim(strtolower(str_replace('SOLUTCLOUD', '', $order->plan)));

                    // C. Création de la Fiche Entreprise (Engagement 12 mois)
                    $company = Company::create([
                        'name'       => $order->company_name,
                        'email'      => $order->customer_email,
                        'phone'      => $order->customer_phone ?? null,
                        'subdomain'  => (string) Str::of($order->company_name)->slug('')->substr(0, 50), // ex: "I-Solutions CI" -> "i-solutions-ci"
                        'package'    => $cleanPackage,
                        'status'     => 'pending', // Reste orange jusqu'à installation manuelle sur LWS
                        'expires_at' => now()->addMonths(12),
                    ]);

                    /**
                     * D. Création/Mise à jour du Compte Utilisateur
                     * On utilise updateOrCreate pour éviter les crashs si l'email existe déjà
                     */
                    $user = User::updateOrCreate(
                        ['email' => $order->customer_email],
                        [
                            'name'       => $order->customer_name,
                            'password'   => Hash::make(Str::random(12)),
                            'role'       => User::ROLE_CLIENT,
                            'company_id' => $company->id
                        ]
                    );

                    // E. Expédition des emails de confirmation (Brevo)
                    try {
                        Mail::to($order->customer_email)->send(new CustomerOrderConfirmation($order));
                        Mail::to('sales@i-solutions.ci')->send(new SalesNotification($order));
                    } catch (\Exception $e) {
                        Log::error("ERREUR ENVOI EMAILS WEBHOOK : " . $e->getMessage());
                    }
                });

                Log::info("SUCCÈS - Client créé via Moneroo : " . $order->company_name);
            }
        }

        // Réponse obligatoire 200 pour libérer le serveur Moneroo
        return response()->json(['status' => 'processed'], 200);
    }
}