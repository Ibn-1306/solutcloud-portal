<?php

namespace App\Services;

use App\Exceptions\PaymentLinkExpiredException;
use App\Models\Payment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MonerooPaymentService
{
    /**
     * @return array{id: string, checkout_url: string}
     */
    public function initialize(Payment $payment): array
    {
        [$firstName, $lastName] = $this->splitName($payment->customer_name);

        $customer = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $payment->customer_email,
        ];

        if (filled($payment->customer_phone)) {
            $customer['phone'] = $payment->customer_phone;
        }

        $metadata = [
            'payment_id' => (string) $payment->id,
            'payment_reference' => (string) $payment->reference,
            'package' => strtoupper((string) $payment->package),
            'purpose' => (string) ($payment->purpose ?: Payment::PURPOSE_INITIAL),
        ];

        if ($payment->duration_months !== null) {
            $metadata['duration_months'] = (int) $payment->duration_months;
        }

        if ($payment->company_id !== null) {
            $metadata['company_id'] = (int) $payment->company_id;
        }

        $response = $this->request()->post('/v1/payments/initialize', [
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            // Moneroo reçoit toujours un libellé exploitable, sans rendre les
            // précisions du client obligatoires dans SOLUTCLOUD.
            'description' => filled($payment->description)
                ? trim((string) $payment->description)
                : sprintf(
                    'Règlement %s — SOLUTCLOUD %s',
                    $payment->reference,
                    strtoupper((string) $payment->package),
                ),
            'return_url' => route('payments.return'),
            'customer' => $customer,
            'metadata' => $metadata,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException($this->errorMessage($response, 'Impossible d’initialiser le paiement Moneroo.'));
        }

        $transactionId = $response->json('data.id');
        $checkoutUrl = $response->json('data.checkout_url');

        if (! is_string($transactionId) || $transactionId === '' || ! $this->isSecureUrl($checkoutUrl)) {
            throw new RuntimeException('La réponse de paiement Moneroo est incomplète.');
        }

        return [
            'id' => $transactionId,
            'checkout_url' => $checkoutUrl,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function verify(string $transactionId): array
    {
        $response = $this->request()->get('/v1/payments/'.rawurlencode($transactionId).'/verify');

        if (! $response->successful()) {
            $message = mb_strtolower((string) $response->json('message', ''));

            if (in_array($response->status(), [404, 410], true) || str_contains($message, 'expir')) {
                throw new PaymentLinkExpiredException('Le lien de paiement a expiré.');
            }

            throw new RuntimeException($this->errorMessage($response, 'Impossible de vérifier le paiement Moneroo.'));
        }

        $payment = $response->json('data');

        if (! is_array($payment)) {
            throw new RuntimeException('La réponse de vérification Moneroo est incomplète.');
        }

        return $payment;
    }

    public function hasValidWebhookSignature(string $payload, ?string $signature): bool
    {
        $secret = (string) config('services.moneroo.webhook_secret');

        if ($secret === '' || ! is_string($signature) || $signature === '') {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $payload, $secret), $signature);
    }

    private function request(): PendingRequest
    {
        $secret = (string) config('services.moneroo.secret');

        if ($secret === '') {
            throw new RuntimeException('La clé secrète Moneroo n’est pas configurée.');
        }

        return Http::baseUrl(rtrim((string) config('services.moneroo.base_url'), '/'))
            ->withToken($secret)
            ->acceptJson()
            ->asJson()
            ->connectTimeout(4)
            ->timeout((int) config('services.moneroo.timeout', 10));
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2) ?: [];
        $firstName = $parts[0] ?? 'Client';

        return [$firstName, $parts[1] ?? $firstName];
    }

    private function errorMessage(Response $response, string $fallback): string
    {
        $message = $response->json('message');

        return is_string($message) && trim($message) !== '' ? $message : $fallback;
    }

    private function isSecureUrl(mixed $url): bool
    {
        return is_string($url)
            && filter_var($url, FILTER_VALIDATE_URL) !== false
            && strtolower((string) parse_url($url, PHP_URL_SCHEME)) === 'https';
    }
}
