<?php

namespace App\Http\Controllers;

use App\Mail\CustomerOrderConfirmation;
use App\Mail\SalesNotification;
use App\Models\Company;
use App\Models\Order;
use App\Models\Quote;
use App\Models\User;
use App\Services\MonerooPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Tarifs annuels (engagement 12 mois) en FCFA.
     * Le frontend n'envoie PLUS le montant : le serveur le recalcule
     * à partir du plan pour éviter toute falsification.
     */
    private const PRICES = [
        'SOLUTCLOUD START' => 70800,
        'SOLUTCLOUD BUSINESS' => 118800,
    ];

    /**
     * 1. GÉNÉRATION DU LIEN DE PAIEMENT (API PUBLIC)
     * Point d'entrée pour le site vitrine via fetch.
     */
    public function createCheckout(Request $request, MonerooPaymentService $moneroo)
    {
        $data = $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'plan' => 'required|string',
        ]);

        // Sécurité : le montant est calculé côté serveur, jamais lu depuis le frontend
        $normalizedPlan = strtoupper(trim((string) $data['plan']));
        $amount = self::PRICES[$normalizedPlan] ?? null;

        if (! $amount) {
            Log::warning('MONEROO - Plan invalide reçu : '.$normalizedPlan);

            return response()->json(['error' => 'Formule invalide. Contactez support@i-solutions.ci.'], 422);
        }

        try {
            // LOGIQUE SENIOR : Séparation du nom complet pour l'API Moneroo
            $nameParts = explode(' ', trim($data['fullname']), 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? $nameParts[0]; // Sécurité si un seul mot est saisi

            $payment = $moneroo->initialize([
                'amount' => $amount,
                'currency' => 'XOF',
                'customer' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ],
                'description' => 'Activation SOLUTCLOUD - '.$normalizedPlan,
                'return_url' => config('services.moneroo.return_url'),
                'metadata' => [
                    'payment_type' => 'order',
                    'company_name' => $data['company_name'],
                    'plan' => $normalizedPlan,
                ],
            ]);

            Order::create([
                'transaction_id' => $payment['id'],
                'company_name' => $data['company_name'],
                'customer_name' => $data['fullname'],
                'customer_email' => $data['email'],
                'customer_phone' => $data['phone'],
                'plan' => $normalizedPlan,
                'amount' => $amount,
                'status' => 'pending',
            ]);

            return response()->json([
                'status' => 'success',
                'checkout_url' => $payment['checkout_url'],
            ]);

        } catch (\Exception $e) {
            Log::error('CRITICAL - ÉCHEC INITIALISATION MONEROO : '.$e->getMessage());

            return response()->json(['error' => 'Une erreur technique empêche le paiement. Contactez support@i-solutions.ci'], 500);
        }
    }

    /**
     * 2. WEBHOOK : AUTOMATISATION POST-PAIEMENT
     * Cette méthode est appelée par les serveurs de Moneroo en arrière-plan.
     */
    public function handleWebhook(Request $request, MonerooPaymentService $moneroo)
    {
        $signature = $request->header('X-Moneroo-Signature');

        if (! $moneroo->hasValidWebhookSignature($request->getContent(), $signature)) {
            Log::warning('ATTENTION - Webhook Moneroo : signature invalide ou absente.');

            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $payload = $request->all();

        if (($payload['event'] ?? null) !== 'payment.success') {
            return response()->json(['status' => 'processed']);
        }

        $transactionId = $payload['data']['id'] ?? null;

        if (! is_string($transactionId) || $transactionId === '') {
            Log::warning('WEBHOOK MONEROO - transaction_id manquant dans le payload');

            return response()->json(['error' => 'Invalid payload'], 400);
        }

        try {
            $payment = $moneroo->verify($transactionId);
        } catch (\Throwable $e) {
            Log::error('WEBHOOK MONEROO - vérification impossible : '.$e->getMessage());

            return response()->json(['error' => 'Payment verification failed'], 503);
        }

        if (strtolower((string) ($payment['status'] ?? '')) !== 'success'
            || strtoupper((string) ($payment['currency'] ?? '')) !== 'XOF') {
            Log::warning("WEBHOOK MONEROO - paiement {$transactionId} non confirmé ou devise invalide");

            return response()->json(['error' => 'Payment not confirmed'], 422);
        }

        $verifiedAmount = (int) ($payment['amount'] ?? -1);
        $quote = Quote::where('payment_transaction_id', $transactionId)->first();

        if ($quote) {
            if ($verifiedAmount !== (int) $quote->amount) {
                Log::warning("WEBHOOK MONEROO - montant invalide pour le devis {$quote->quote_number}");

                return response()->json(['error' => 'Amount mismatch'], 422);
            }

            if (! $quote->isPaid()) {
                $quote->update([
                    'status' => Quote::STATUS_PAID,
                    'paid_at' => now(),
                ]);
            }

            Log::info("PAIEMENT DEVIS CONFIRMÉ - {$quote->quote_number}");

            return response()->json(['status' => 'processed']);
        }

        $order = Order::where('transaction_id', $transactionId)->first();

        if (! $order) {
            Log::warning("WEBHOOK MONEROO - transaction inconnue : {$transactionId}");

            return response()->json(['status' => 'processed']);
        }

        if ($verifiedAmount !== (int) $order->amount) {
            Log::warning("WEBHOOK MONEROO - montant invalide pour la commande {$transactionId}");

            return response()->json(['error' => 'Amount mismatch'], 422);
        }

        // Sécurité : on ne traite que les commandes existantes et non encore validées.
        if ($order->status === 'pending') {
            DB::transaction(function () use ($order) {
                $order->update(['status' => 'completed']);

                $cleanPackage = trim(strtolower(str_replace('SOLUTCLOUD', '', $order->plan)));

                $company = Company::create([
                    'name' => $order->company_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone ?? null,
                    'subdomain' => (string) Str::of($order->company_name)->slug('')->substr(0, 50),
                    'package' => $cleanPackage,
                    'status' => 'pending',
                    'expires_at' => now()->addMonths(12),
                ]);

                User::updateOrCreate(
                    ['email' => $order->customer_email],
                    [
                        'name' => $order->customer_name,
                        'password' => Hash::make(Str::random(12)),
                        'role' => User::ROLE_CLIENT,
                        'company_id' => $company->id,
                    ]
                );

                try {
                    Mail::to($order->customer_email)->send(new CustomerOrderConfirmation($order));
                    Mail::to('sales@i-solutions.ci')->send(new SalesNotification($order));
                } catch (\Exception $e) {
                    Log::error('ERREUR ENVOI EMAILS WEBHOOK : '.$e->getMessage());
                }
            });

            Log::info('SUCCÈS - Client créé via Moneroo : '.$order->company_name);
        }

        return response()->json(['status' => 'processed']);
    }
}
