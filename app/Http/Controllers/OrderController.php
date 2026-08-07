<?php

namespace App\Http\Controllers;

use App\Models\{Order, Company, User};
use App\Mail\{CustomerOrderConfirmation, SalesNotification};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Http, Log, Mail, DB, Hash};
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * 1. GÉNÉRATION DU LIEN DE PAIEMENT (API PUBLIC)
     * Reçoit les données du site vitrine et initie la transaction Moneroo.
     */
    public function createCheckout(Request $request)
    {
        // Validation stricte des données d'entrée
        $data = $request->validate([
            'fullname'     => 'required|string|max:255',
            'email'        => 'required|email',
            'company_name' => 'required|string|max:255',
            'phone'        => 'required|string',
            'amount'       => 'required|numeric',
            'plan'         => 'required|string',
        ]);

        try {
            // Appel à l'API Moneroo
            $response = Http::withToken(env('MONEROO_SECRET_KEY'))
                ->post('https://api.moneroo.io/v1/payments/initialize', [
                    'amount'      => (int)$data['amount'],
                    'currency'    => 'XOF',
                    'customer'    => [
                        'name'  => $data['fullname'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                    ],
                    'description' => "Activation SOLUTCLOUD - " . $data['plan'],
                    'return_url'  => 'https://www.solutcloud.com/merci.html',
                    'cancel_url'  => 'https://www.solutcloud.com/tarifs.html',
                    'metadata'    => [
                        'company_name' => $data['company_name'],
                        'plan'         => $data['plan']
                    ]
                ]);

            $result = $response->json();

            if ($response->successful() && isset($result['data']['checkout_url'])) {
                
                // Enregistrement de la commande en base avec statut 'pending'
                Order::create([
                    'transaction_id' => $result['data']['id'], 
                    'company_name'   => $data['company_name'],
                    'customer_name'  => $data['fullname'],
                    'customer_email' => $data['email'],
                    'customer_phone' => $data['phone'],
                    'plan'           => $data['plan'],
                    'amount'         => (int)$data['amount'],
                    'status'         => 'pending'
                ]);

                return response()->json([
                    'status'       => 'success',
                    'checkout_url' => $result['data']['checkout_url']
                ]);
            }

            throw new \Exception($result['message'] ?? 'Erreur API Moneroo');

        } catch (\Exception $e) {
            Log::error("ERREUR INITIALISATION PAIEMENT : " . $e->getMessage());
            return response()->json(['error' => 'Une erreur technique est survenue. Veuillez réessayer.'], 500);
        }
    }

    /**
     * 2. WEBHOOK : TRAITEMENT POST-PAIEMENT
     * Reçoit la confirmation de Moneroo (Serveur à Serveur).
     */
    public function handleWebhook(Request $request)
    {
        // Sécurité : Vérification de la signature du Webhook
        $signature = $request->header('moneroo-signature');
        if (!$signature || $signature !== env('MONEROO_WEBHOOK_SECRET')) {
            Log::warning("Webhook Moneroo : Signature invalide.");
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        if (isset($payload['event']) && $payload['event'] === 'payment.success') {
            
            $transactionId = $payload['data']['id'];
            $order = Order::where('transaction_id', $transactionId)->first();

            // Si la commande existe et n'est pas encore traitée
            if ($order && $order->status === 'pending') {
                
                DB::transaction(function () use ($order) {
                    // 1. Mise à jour de la commande
                    $order->update(['status' => 'completed']);

                    // 2. CRÉATION AUTOMATIQUE DE LA FICHE CLIENT (Statut : pending install)
                    // On transforme le nom de l'entreprise en identifiant (slug)
                    $subdomain = Str::slug($order->company_name);

                    $company = Company::create([
                        'name'       => $order->company_name,
                        'email'      => $order->customer_email,
                        'subdomain'  => $subdomain,
                        'package'    => strtolower($order->plan),
                        'status'     => 'pending', // Attend l'intervention admin
                        'total_paid' => $order->amount,
                    ]);

                    // 3. Création du compte utilisateur pour le portail login.solutcloud.com
                    User::create([
                        'name'       => $order->customer_name,
                        'email'      => $order->customer_email,
                        'password'   => Hash::make(Str::random(12)), // MDP aléatoire à changer
                        'role'       => 'client',
                        'company_id' => $company->id
                    ]);

                    // 4. NOTIFICATIONS EMAILS
                    try {
                        Mail::to($order->customer_email)->send(new CustomerOrderConfirmation($order));
                        Mail::to('sales@i-solutions.ci')->send(new SalesNotification($order));
                    } catch (\Exception $e) {
                        Log::error("ECHEC ENVOI MAILS WEBHOOK : " . $e->getMessage());
                    }
                });

                Log::info("Paiement Moneroo validé et fiche client créée : " . $order->company_name);
            }
        }

        return response()->json(['status' => 'processed'], 200);
    }
}