<?php

namespace Tests\Feature;

use App\Mail\NewsletterWelcome;
use App\Mail\WebsiteLeadAcknowledgement;
use App\Mail\WebsiteLeadReceived;
use App\Models\NewsletterSubscriber;
use App\Models\WebsiteLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicWebsiteFormsTest extends TestCase
{
    use RefreshDatabase;

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

        $this->assertDatabaseCount('website_leads', 0);
        Mail::assertNothingSent();
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
