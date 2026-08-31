<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\VerifyMonerooPayment;
use App\Models\Payment;
use App\Models\PaymentCheckoutAttempt;
use App\Services\MonerooPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonerooWebhookController extends Controller
{
    public function __invoke(Request $request, MonerooPaymentService $moneroo): JsonResponse
    {
        $payload = $request->getContent();

        if (! $moneroo->hasValidWebhookSignature($payload, $request->header('X-Moneroo-Signature'))) {
            return response()->json(['error' => 'Signature invalide.'], 403);
        }

        $decoded = json_decode($payload, true);
        $event = is_array($decoded) ? ($decoded['event'] ?? null) : null;
        $monerooPaymentId = is_array($decoded) ? ($decoded['data']['id'] ?? null) : null;

        if (! is_string($event) || ! str_starts_with($event, 'payment.')) {
            return response()->json(['status' => 'ignored']);
        }

        if (! is_string($monerooPaymentId) || $monerooPaymentId === '') {
            return response()->json(['error' => 'Identifiant de paiement absent.'], 422);
        }

        $payment = Payment::where('moneroo_payment_id', $monerooPaymentId)->first()
            ?? PaymentCheckoutAttempt::query()
                ->where('moneroo_payment_id', $monerooPaymentId)
                ->with('payment')
                ->first()
                ?->payment;

        if ($payment === null) {
            Log::warning('MONEROO_WEBHOOK_PAYMENT_UNKNOWN', [
                'moneroo_payment_id' => $monerooPaymentId,
                'event' => $event,
            ]);

            return response()->json(['status' => 'ignored']);
        }

        VerifyMonerooPayment::dispatch($payment->id, $monerooPaymentId)->onConnection('deferred');

        return response()->json(['status' => 'received']);
    }
}
