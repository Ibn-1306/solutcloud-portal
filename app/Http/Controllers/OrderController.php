<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // 1. Génère le lien Moneroo et pré-enregistre la commande
    public function createCheckout(Request $request)
    {
        $response = Http::withToken(env('MONEROO_SECRET_KEY'))
            ->post('https://api.moneroo.io/v1/payments/checkout', [
                'amount'      => (int)$request->amount,
                'currency'    => 'XOF',
                'customer'    => [
                    'name'  => $request->fullname,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ],
                'description' => "Activation SOLUTCLOUD - " . $request->plan,
                'return_url'  => 'https://solutcloud.com/success.html',
                'cancel_url'  => 'https://solutcloud.com/tarifs.html',
                'metadata'    => [
                    'company_name' => $request->company_name,
                    'plan'         => $request->plan
                ]
            ]);

        $result = $response->json();

        if ($response->successful()) {
            // Enregistrement de la commande en attente
            Order::create([
                'transaction_id' => $result['data']['id'],
                'company_name'   => $request->company_name,
                'customer_name'  => $request->fullname,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'plan'           => $request->plan,
                'amount'         => (int)$request->amount,
                'status'         => 'pending'
            ]);

            return response()->json(['checkout_url' => $result['data']['checkout_url']]);
        }

        return response()->json(['error' => 'Erreur Moneroo : ' . ($result['message'] ?? 'Inconnue')], 500);
    }
}