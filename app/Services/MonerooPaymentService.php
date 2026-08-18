<?php

namespace App\Services;

use App\Models\Quote;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MonerooPaymentService
{
    /**
     * @return array{id: string, checkout_url: string}
     */
    public function initializeQuote(Quote $quote): array
    {
        $nameParts = explode(' ', trim($quote->customer_name), 2);
        $customer = [
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? $nameParts[0],
            'email' => $quote->customer_email,
        ];

        if (is_string($quote->customer_phone) && trim($quote->customer_phone) !== '') {
            $customer['phone'] = $quote->customer_phone;
        }

        return $this->initialize([
            'amount' => (int) $quote->amount,
            'currency' => 'XOF',
            'customer' => $customer,
            'description' => "Règlement du devis SOLUTCLOUD {$quote->quote_number}",
            'return_url' => config('services.moneroo.return_url'),
            'metadata' => [
                'payment_type' => 'quote',
                'quote_id' => (string) $quote->id,
                'quote_number' => $quote->quote_number,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{id: string, checkout_url: string}
     */
    public function initialize(array $payload): array
    {
        $response = $this->request()->post('/v1/payments/initialize', $payload);

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
            ->timeout((int) config('services.moneroo.timeout', 10));
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
