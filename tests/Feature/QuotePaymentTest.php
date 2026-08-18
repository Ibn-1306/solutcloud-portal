<?php

namespace Tests\Feature;

use App\Mail\QuoteSendMail;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QuotePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2026-08-18 10:00:00');

        config([
            'services.moneroo.secret' => 'test_secret_key',
            'services.moneroo.webhook_secret' => 'test_webhook_secret',
            'services.moneroo.base_url' => 'https://sandbox.moneroo.io',
            'services.moneroo.return_url' => 'https://solutcloud.com/tarifs.html',
        ]);
    }

    public function test_an_admin_creates_a_quote_with_a_moneroo_payment_link(): void
    {
        Mail::fake();
        Http::fake([
            'https://sandbox.moneroo.io/v1/payments/initialize' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 'pay_quote_001',
                    'checkout_url' => 'https://checkout.moneroo.io/pay_quote_001',
                ],
            ], 201),
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->post(route('admin.quotes.store'), [
            'customer_name' => 'Awa Koné',
            'customer_email' => 'awa@example.com',
            'customer_phone' => '+225 01 02 03 04 05',
            'company_name' => 'Entreprise Démonstration',
            'amount' => 350000,
            'duration' => 12,
            'description' => 'Déploiement et accompagnement.',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $quote = Quote::firstOrFail();

        $this->assertSame('DEVIS-26-0001', $quote->quote_number);
        $this->assertSame('pay_quote_001', $quote->payment_transaction_id);
        $this->assertSame('https://checkout.moneroo.io/pay_quote_001', $quote->payment_url);
        $this->assertSame(Quote::STATUS_SENT, $quote->status);
        $this->assertNotNull($quote->payment_initialized_at);

        Http::assertSent(function (Request $request) use ($quote): bool {
            return $request->url() === 'https://sandbox.moneroo.io/v1/payments/initialize'
                && $request->hasHeader('Authorization', 'Bearer test_secret_key')
                && $request['amount'] === 350000
                && $request['currency'] === 'XOF'
                && $request['metadata']['quote_id'] === (string) $quote->id
                && $request['metadata']['quote_number'] === 'DEVIS-26-0001';
        });

        Mail::assertSent(QuoteSendMail::class, function (QuoteSendMail $mail): bool {
            return $mail->hasTo('awa@example.com')
                && $mail->quote->payment_url === 'https://checkout.moneroo.io/pay_quote_001';
        });
    }

    public function test_a_verified_moneroo_webhook_marks_the_quote_as_paid(): void
    {
        $quote = $this->createPayableQuote();

        Http::fake([
            'https://sandbox.moneroo.io/v1/payments/pay_quote_001/verify' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 'pay_quote_001',
                    'amount' => 350000,
                    'currency' => 'XOF',
                    'status' => 'success',
                ],
            ]),
        ]);

        $payload = json_encode([
            'event' => 'payment.success',
            'data' => ['id' => 'pay_quote_001'],
        ], JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $payload, 'test_webhook_secret');

        $this->call(
            'POST',
            '/api/moneroo-webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_MONEROO_SIGNATURE' => $signature,
            ],
            $payload,
        )->assertOk()->assertJsonPath('status', 'processed');

        $quote->refresh();

        $this->assertSame(Quote::STATUS_PAID, $quote->status);
        $this->assertNotNull($quote->paid_at);
    }

    public function test_the_quote_is_not_paid_when_the_signature_or_amount_is_invalid(): void
    {
        $quote = $this->createPayableQuote();
        $payload = json_encode([
            'event' => 'payment.success',
            'data' => ['id' => 'pay_quote_001'],
        ], JSON_THROW_ON_ERROR);

        $this->call(
            'POST',
            '/api/moneroo-webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_MONEROO_SIGNATURE' => 'invalid',
            ],
            $payload,
        )->assertForbidden();

        Http::fake([
            'https://sandbox.moneroo.io/v1/payments/pay_quote_001/verify' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 'pay_quote_001',
                    'amount' => 1,
                    'currency' => 'XOF',
                    'status' => 'success',
                ],
            ]),
        ]);

        $signature = hash_hmac('sha256', $payload, 'test_webhook_secret');

        $this->call(
            'POST',
            '/api/moneroo-webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_MONEROO_SIGNATURE' => $signature,
            ],
            $payload,
        )->assertUnprocessable();

        $this->assertSame(Quote::STATUS_SENT, $quote->fresh()->status);
        $this->assertNull($quote->fresh()->paid_at);
    }

    private function createPayableQuote(): Quote
    {
        return Quote::create([
            'quote_number' => 'DEVIS-26-0001',
            'payment_transaction_id' => 'pay_quote_001',
            'payment_url' => 'https://checkout.moneroo.io/pay_quote_001',
            'payment_initialized_at' => now(),
            'customer_name' => 'Awa Koné',
            'customer_email' => 'awa@example.com',
            'customer_phone' => '+225 01 02 03 04 05',
            'company_name' => 'Entreprise Démonstration',
            'amount' => 350000,
            'duration' => 12,
            'status' => Quote::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }
}
