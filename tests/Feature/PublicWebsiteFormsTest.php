<?php

namespace Tests\Feature;

use App\Jobs\SendWebsiteLeadEmails;
use App\Mail\NewsletterWelcome;
use App\Mail\WebsiteLeadAcknowledgement;
use App\Mail\WebsiteLeadReceived;
use App\Models\NewsletterSubscriber;
use App\Models\WebsiteLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PublicWebsiteFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_emails_are_dispatched_after_the_api_response(): void
    {
        Mail::fake();
        Queue::fake();
        config(['services.solutcloud.contact_recipient' => 'sales@example.com']);

        $this->postJson('/api/leads', [
            'type' => 'order',
            'offer' => 'START',
            'fullname' => 'Awa Koné',
            'email' => 'awa@example.com',
            'phone' => '+225 01 02 03 04 05',
            'company_name' => 'Entreprise Démo',
            'profile' => 'Direction',
            'message' => 'Commande SOLUTCLOUD START.',
        ])->assertCreated();

        Queue::assertPushed(SendWebsiteLeadEmails::class, function (SendWebsiteLeadEmails $job): bool {
            $this->assertSame('deferred', $job->connection);

            return $job->salesRecipient === 'sales@example.com';
        });
        Mail::assertNothingSent();
    }

    public function test_a_website_lead_is_stored_and_sent_to_sales(): void
    {
        Mail::fake();
        config(['services.solutcloud.contact_recipient' => 'sales@example.com']);

        $response = $this->postJson('/api/leads', [
            'type' => 'contact',
            'fullname' => 'Awa Koné',
            'email' => 'AWA@example.com',
            'phone' => '+225 01 02 03 04 05',
            'company_name' => 'Entreprise Démo',
            'profile' => 'Direction',
            'message' => 'Je souhaite être rappelée.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('website_leads', [
            'type' => 'contact',
            'fullname' => 'Awa Koné',
            'email' => 'awa@example.com',
            'company_name' => 'Entreprise Démo',
            'phone' => '+2250102030405',
        ]);

        Mail::assertSent(WebsiteLeadReceived::class, function (WebsiteLeadReceived $mail): bool {
            return $mail->hasTo('sales@example.com')
                && $mail->lead->notified_at !== null;
        });
        Mail::assertSent(WebsiteLeadAcknowledgement::class, function (WebsiteLeadAcknowledgement $mail): bool {
            return $mail->hasTo('awa@example.com')
                && $mail->lead->acknowledged_at !== null;
        });

        $mailHtml = (new WebsiteLeadReceived(WebsiteLead::firstOrFail()))->render();
        $this->assertStringContainsString('Awa Koné', $mailHtml);
        $this->assertStringContainsString('Je souhaite être rappelée.', $mailHtml);

        $acknowledgementHtml = (new WebsiteLeadAcknowledgement(WebsiteLead::firstOrFail()))->render();
        $this->assertStringContainsString('Votre message a bien été transmis', $acknowledgementHtml);
    }

    public function test_order_can_be_submitted_without_additional_notes(): void
    {
        Queue::fake();
        config(['services.solutcloud.contact_recipient' => 'sales@example.com']);

        $this->postJson('/api/leads', [
            'type' => 'order',
            'offer' => 'BUSINESS',
            'fullname' => 'Mariam Traoré',
            'email' => 'mariam@example.com',
            'phone' => '+225 05 06 07 08 09',
            'company_name' => 'Entreprise Business',
            'profile' => 'PME',
        ])->assertCreated();

        $this->assertDatabaseHas('website_leads', [
            'type' => 'order',
            'offer' => 'BUSINESS',
            'email' => 'mariam@example.com',
            'message' => null,
        ]);
    }

    public function test_invalid_leads_are_rejected_without_sending_mail(): void
    {
        Mail::fake();

        $this->postJson('/api/leads', [
            'type' => 'unknown',
            'fullname' => '',
            'email' => 'not-an-email',
        ])->assertUnprocessable();

        $this->postJson('/api/leads', [
            'type' => 'trial',
            'fullname' => 'Test Client',
            'email' => 'client@example.com',
            'message' => 'Demande de test.',
        ])->assertUnprocessable()->assertJsonValidationErrors(['phone', 'company_name']);

        $this->postJson('/api/leads', [
            'type' => 'trial',
            'fullname' => 'Test Client',
            'email' => 'client@example.com',
            'phone' => '+225 12 34',
            'company_name' => 'Entreprise Test',
            'message' => 'Demande de test.',
        ])->assertUnprocessable()->assertJsonValidationErrors(['phone']);

        $this->assertDatabaseCount('website_leads', 0);
        Mail::assertNothingSent();
    }

    public function test_orders_and_quote_requests_are_saved_and_emailed_to_sales(): void
    {
        Mail::fake();
        config(['services.solutcloud.contact_recipient' => 'sales@i-solutions.ci']);

        $requests = [
            [
                'type' => 'order',
                'offer' => 'START',
                'fullname' => 'Awa Koné',
                'email' => 'awa@example.com',
                'phone' => '+225 01 02 03 04 05',
                'company_name' => 'Entreprise Alpha',
                'profile' => 'PME',
                'message' => 'Commande SOLUTCLOUD START.',
            ],
            [
                'type' => 'quote',
                'offer' => 'PREMIUM',
                'fullname' => 'Jean Kouassi',
                'email' => 'jean@example.com',
                'phone' => '+33 6 12 34 56 78',
                'company_name' => 'Entreprise Premium',
                'profile' => 'PME',
                'message' => 'Demande de devis PREMIUM.',
            ],
        ];

        foreach ($requests as $request) {
            $this->postJson('/api/leads', $request)
                ->assertCreated()
                ->assertJsonPath('status', 'success');
        }

        $this->assertDatabaseHas('website_leads', [
            'type' => 'order',
            'offer' => 'START',
            'email' => 'awa@example.com',
        ]);
        $this->assertDatabaseHas('website_leads', [
            'type' => 'quote',
            'offer' => 'PREMIUM',
            'email' => 'jean@example.com',
            'phone' => '+33612345678',
        ]);

        Mail::assertSent(WebsiteLeadReceived::class, 2);
        Mail::assertSent(WebsiteLeadReceived::class, function (WebsiteLeadReceived $mail): bool {
            return $mail->hasTo('sales@i-solutions.ci')
                && in_array($mail->lead->type, ['order', 'quote'], true);
        });
        Mail::assertSent(WebsiteLeadAcknowledgement::class, 2);
    }

    public function test_newsletter_subscription_is_idempotent_and_sends_one_welcome_email(): void
    {
        Mail::fake();

        $this->postJson('/api/newsletter', ['email' => 'NEWS@example.com'])
            ->assertCreated()
            ->assertJsonPath('status', 'success');

        $this->postJson('/api/newsletter', ['email' => 'news@example.com'])
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseCount('newsletter_subscribers', 1);
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'news@example.com',
            'is_active' => true,
        ]);

        Mail::assertSent(NewsletterWelcome::class, 1);
        Mail::assertSent(NewsletterWelcome::class, function (NewsletterWelcome $mail): bool {
            return $mail->hasTo('news@example.com')
                && $mail->subscriber->welcome_sent_at !== null;
        });

        $mailHtml = (new NewsletterWelcome(NewsletterSubscriber::firstOrFail()))->render();
        $this->assertStringContainsString('Bienvenue dans l’écosystème SOLUTCLOUD', $mailHtml);
    }

    public function test_invalid_newsletter_email_is_rejected(): void
    {
        Mail::fake();

        $this->postJson('/api/newsletter', ['email' => 'invalid'])
            ->assertUnprocessable();

        $this->assertDatabaseCount('newsletter_subscribers', 0);
        Mail::assertNothingSent();
    }
}
