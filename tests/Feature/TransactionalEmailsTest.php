<?php

namespace Tests\Feature;

use App\Mail\CustomerOrderConfirmation;
use App\Mail\DemoAccessMail;
use App\Mail\InstanceReadyMail;
use App\Mail\NewsletterWelcome;
use App\Mail\QuoteSendMail;
use App\Mail\SalesNotification;
use App\Mail\WebsiteLeadAcknowledgement;
use App\Mail\WebsiteLeadReceived;
use App\Models\Company;
use App\Models\Demo;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\Quote;
use App\Models\WebsiteLead;
use Illuminate\Mail\Mailable;
use Tests\TestCase;

class TransactionalEmailsTest extends TestCase
{
    public function test_every_transactional_email_uses_the_professional_layout(): void
    {
        $order = new Order([
            'transaction_id' => 'TX-SC-2026-001',
            'company_name' => 'Entreprise Démonstration',
            'customer_name' => 'Awa Koné',
            'customer_email' => 'awa@example.com',
            'customer_phone' => '+225 01 02 03 04 05',
            'plan' => 'premium',
            'amount' => 250000,
            'status' => 'paid',
        ]);

        $demo = new Demo([
            'company_name' => 'Entreprise Démonstration',
            'subdomain' => 'entreprise-demo',
            'email' => 'awa@example.com',
            'phone' => '+225 01 02 03 04 05',
            'erp_login' => 'admin.demo',
            'erp_password' => 'mot-de-passe-test',
        ]);

        $quote = new Quote([
            'quote_number' => 'DEV-2026-0001',
            'customer_name' => 'Awa Koné',
            'customer_email' => 'awa@example.com',
            'customer_phone' => '+225 01 02 03 04 05',
            'company_name' => 'Entreprise Démonstration',
            'amount' => 350000,
            'duration' => '12 mois',
            'description' => 'Déploiement, configuration et accompagnement.',
            'status' => Quote::STATUS_SENT,
        ]);

        $company = new Company([
            'name' => 'Entreprise Démonstration',
            'email' => 'awa@example.com',
            'subdomain' => 'entreprise-demo',
            'package' => 'premium',
            'status' => 'active',
        ]);

        $lead = new WebsiteLead([
            'type' => 'contact',
            'fullname' => 'Awa Koné',
            'email' => 'awa@example.com',
            'phone' => '+225 01 02 03 04 05',
            'company_name' => 'Entreprise Démonstration',
            'profile' => 'Direction',
            'message' => 'Je souhaite être rappelée.',
        ]);
        $lead->id = 42;

        $subscriber = new NewsletterSubscriber([
            'email' => 'awa@example.com',
            'is_active' => true,
        ]);

        $messages = [
            [new CustomerOrderConfirmation($order), 'Votre paiement est confirmé'],
            [new SalesNotification($order), 'Nouvelle commande à provisionner'],
            [new DemoAccessMail($demo), 'Votre démonstration est prête'],
            [new InstanceReadyMail($company, 'https://entreprise-demo.solutcloud.com', 'admin.demo', 'mot-de-passe-test'), 'Votre instance est opérationnelle'],
            [new QuoteSendMail($quote), 'Votre proposition SOLUTCLOUD PREMIUM'],
            [new WebsiteLeadAcknowledgement($lead), 'Votre message a bien été transmis'],
            [new WebsiteLeadReceived($lead), 'Nouvelle demande commerciale'],
            [new NewsletterWelcome($subscriber), 'Bienvenue dans l’écosystème SOLUTCLOUD'],
        ];

        foreach ($messages as [$mailable, $expectedHeading]) {
            $this->assertProfessionalLayout($mailable, $expectedHeading);
        }
    }

    private function assertProfessionalLayout(Mailable $mailable, string $expectedHeading): void
    {
        $html = $mailable->render();
        $subject = $mailable->envelope()->subject;

        $this->assertStringContainsString('<!doctype html>', $html);
        $this->assertStringContainsString('SOLUTCLOUD', $html);
        $this->assertStringContainsString('I-SOLUTIONS', $html);
        $this->assertStringContainsString('role="presentation"', $html);
        $this->assertStringContainsString($expectedHeading, $html);
        $this->assertStringContainsString('SOLUTCLOUD', $subject);
        $this->assertStringNotContainsString('🚀', $html);
        $this->assertStringNotContainsString('💰', $html);
        $this->assertStringNotContainsString('🚀', $subject);
        $this->assertStringNotContainsString('💰', $subject);
        $this->assertStringNotContainsString('✅', $subject);
    }
}
