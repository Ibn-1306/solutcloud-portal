<?php

namespace Tests\Feature;

use App\Mail\AccountInvitationMail;
use App\Mail\DemoAccessMail;
use App\Mail\InstallationPendingMail;
use App\Mail\InstanceReadyMail;
use App\Mail\NewsletterWelcome;
use App\Mail\PaymentLinkMail;
use App\Mail\WebsiteLeadAcknowledgement;
use App\Mail\WebsiteLeadReceived;
use App\Models\Company;
use App\Models\Demo;
use App\Models\NewsletterSubscriber;
use App\Models\Payment;
use App\Models\User;
use App\Models\WebsiteLead;
use Illuminate\Mail\Mailable;
use Tests\TestCase;

class TransactionalEmailsTest extends TestCase
{
    public function test_every_transactional_email_uses_the_professional_layout(): void
    {
        $demo = new Demo([
            'company_name' => 'Entreprise Démonstration',
            'subdomain' => 'entreprise-demo',
            'email' => 'awa@example.com',
            'phone' => '+225 01 02 03 04 05',
            'erp_login' => 'admin.demo',
            'erp_password' => 'mot-de-passe-test',
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

        $orderLead = new WebsiteLead([
            'type' => 'order',
            'fullname' => 'Awa Koné',
            'email' => 'awa@example.com',
            'phone' => '+225 01 02 03 04 05',
            'company_name' => 'Entreprise Démonstration',
            'profile' => 'PME',
            'offer' => 'START',
            'message' => 'Commande SOLUTCLOUD START.',
        ]);
        $orderLead->id = 43;

        $quoteLead = new WebsiteLead([
            'type' => 'quote',
            'fullname' => 'Awa Koné',
            'email' => 'awa@example.com',
            'phone' => '+225 01 02 03 04 05',
            'company_name' => 'Entreprise Démonstration',
            'profile' => 'PME',
            'offer' => 'PREMIUM',
            'message' => 'Demande de devis SOLUTCLOUD PREMIUM.',
        ]);
        $quoteLead->id = 44;

        $subscriber = new NewsletterSubscriber([
            'email' => 'awa@example.com',
            'is_active' => true,
        ]);

        $payment = new Payment([
            'reference' => 'PAY-26-0042',
            'customer_name' => 'Awa Koné',
            'customer_email' => 'awa@example.com',
            'company_name' => 'Entreprise Démonstration',
            'package' => 'premium',
            'amount' => 2000,
            'currency' => 'USD',
            'description' => 'Abonnement annuel SOLUTCLOUD PREMIUM',
            'checkout_url' => 'https://checkout.moneroo.io/pay_test_42',
        ]);

        $user = new User([
            'name' => 'Awa Koné',
            'email' => 'awa@example.com',
            'role' => User::ROLE_CLIENT,
        ]);
        $user->id = 42;

        $messages = [
            [new DemoAccessMail($demo), 'Votre démonstration est prête'],
            [new InstanceReadyMail($company, 'https://entreprise-demo.solutcloud.com', 'admin.demo', 'mot-de-passe-test'), 'Votre instance est opérationnelle'],
            [new InstallationPendingMail($company), 'Votre instance est en cours de préparation'],
            [new AccountInvitationMail($user, 'https://login.solutcloud.com/reset-password/test-token?email=awa%40example.com', $company, $payment), 'Activez votre espace client'],
            [new PaymentLinkMail($payment), 'Votre règlement SOLUTCLOUD'],
            [new WebsiteLeadAcknowledgement($lead), 'Votre message a bien été transmis'],
            [new WebsiteLeadReceived($lead), 'Nouvelle demande commerciale'],
            [new WebsiteLeadAcknowledgement($orderLead), 'Votre commande est confirmée'],
            [new WebsiteLeadReceived($orderLead), 'Nouvelle commande à traiter'],
            [new WebsiteLeadAcknowledgement($quoteLead), 'Votre demande de devis est confirmée'],
            [new WebsiteLeadReceived($quoteLead), 'Nouvelle demande de devis'],
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
        $this->assertStringContainsString('alt="SOLUTCLOUD"', $html);
        $this->assertStringContainsString('data-email-footer="centered"', $html);
        $this->assertStringContainsString('align="center" data-email-footer="centered"', $html);
        $this->assertStringContainsString($expectedHeading, $html);
        $this->assertStringContainsString('#2b909a', strtolower($html));
        $this->assertStringNotContainsString('height:5px;background:#2b909a', strtolower($html));
        $this->assertStringNotContainsString('background:#102a2d', strtolower($html));
        $this->assertStringNotContainsString('background:#e9f5f5', strtolower($html));
        $this->assertStringNotContainsString('cid:', strtolower($html));
        $this->assertStringNotContainsString('$message->embed', strtolower($html));
        $this->assertStringNotContainsString('vient de transmettre une demande depuis le site solutcloud.com', $html);
        $this->assertStringNotContainsString('Message transactionnel envoyé par SOLUTCLOUD.', $html);
        $this->assertStringNotContainsString('Ceci est un accusé de réception automatique.', $html);
        $this->assertStringContainsString('SOLUTCLOUD', $subject);
        $this->assertStringNotContainsString('🚀', $html);
        $this->assertStringNotContainsString('💰', $html);
        $this->assertStringNotContainsString('🚀', $subject);
        $this->assertStringNotContainsString('💰', $subject);
        $this->assertStringNotContainsString('✅', $subject);
        $content = $mailable->content();
        $envelope = $mailable->envelope();
        $headers = $mailable->headers();

        $this->assertNotNull($content->text, $mailable::class.' doit fournir une version texte.');
        $this->assertSame(config('mail.from.address'), $envelope->from->address);
        $this->assertSame(config('mail.from.name'), $envelope->from->name);
        $this->assertNotEmpty($envelope->replyTo);
        $this->assertArrayHasKey('X-Mailin-Tag', $headers->text);
        $this->assertSame('OOF, AutoReply', $headers->text['X-Auto-Response-Suppress']);

        if ($mailable instanceof WebsiteLeadReceived) {
            $this->assertSame($mailable->lead->email, $envelope->replyTo[0]->address);
        } else {
            $this->assertSame(config('mail.reply_to.address'), $envelope->replyTo[0]->address);
        }

        if ($mailable instanceof PaymentLinkMail || $mailable instanceof AccountInvitationMail) {
            $this->assertStringContainsString('class="email-button"', $html);
        } else {
            $this->assertStringNotContainsString('class="email-button"', $html);
        }
    }

    public function test_commercial_confirmations_match_each_website_request(): void
    {
        $requests = [
            [
                'type' => 'order',
                'offer' => 'START',
                'heading' => 'Votre commande est confirmée',
                'subject' => 'Confirmation de votre commande START',
                'reference' => 'CMD-'.now()->format('y').'-0043',
                'detail' => 'validera avec vous les informations de la commande',
            ],
            [
                'type' => 'quote',
                'offer' => 'PREMIUM',
                'heading' => 'Votre demande de devis est confirmée',
                'subject' => 'Confirmation de votre demande de devis PREMIUM',
                'reference' => 'DEVIS-REQ-'.now()->format('y').'-0043',
                'detail' => 'étudiera votre besoin et vous contactera',
            ],
            [
                'type' => 'trial',
                'offer' => null,
                'heading' => 'Votre demande de test est confirmée',
                'subject' => 'Confirmation de votre demande de test',
                'reference' => 'TEST-'.now()->format('y').'-0043',
                'detail' => 'préparer votre accès de test',
            ],
        ];

        foreach ($requests as $request) {
            $lead = new WebsiteLead([
                'type' => $request['type'],
                'offer' => $request['offer'],
                'fullname' => 'Awa Koné',
                'email' => 'awa@example.com',
                'phone' => '+225 01 02 03 04 05',
                'company_name' => 'Entreprise Démonstration',
                'profile' => 'PME',
                'message' => 'Demande transmise depuis solutcloud.com.',
            ]);
            $lead->id = 43;

            $mailable = new WebsiteLeadAcknowledgement($lead);
            $html = $mailable->render();

            $this->assertStringContainsString($request['heading'], $html);
            $this->assertStringContainsString($request['reference'], $html);
            $this->assertStringContainsString($request['detail'], $html);
            $this->assertStringContainsString($request['subject'], $mailable->envelope()->subject);
        }
    }

    public function test_payment_button_is_large_centered_and_highly_visible(): void
    {
        $payment = new Payment([
            'reference' => 'PAY-26-0042',
            'customer_name' => 'Awa Koné',
            'customer_email' => 'awa@example.com',
            'company_name' => 'Entreprise Démonstration',
            'package' => 'start',
            'amount' => 70800,
            'currency' => 'XOF',
            'checkout_url' => 'https://checkout.moneroo.io/pay_test_42',
        ]);

        $html = (new PaymentLinkMail($payment))->render();

        $this->assertStringContainsString('width="420"', $html);
        $this->assertStringContainsString('align="center"', $html);
        $this->assertStringContainsString('padding:18px 32px', $html);
        $this->assertStringContainsString('font-size:16px', $html);
        $this->assertStringContainsString('Payer en toute sécurité', $html);
        $this->assertStringNotContainsString('Vérifiez que l’adresse ouverte appartient bien au domaine sécurisé de Moneroo', $html);
        $this->assertStringNotContainsString('SOLUTCLOUD ne vous demandera jamais vos codes secrets', $html);

        $content = (new PaymentLinkMail($payment))->content();
        $headers = (new PaymentLinkMail($payment))->headers();

        $this->assertSame('emails.text.payment_link', $content->text);
        $this->assertSame('solutcloud-payment-link', $headers->text['X-Mailin-Tag']);
        $this->assertSame('solutcloud-payment-PAY-26-0042', $headers->text['X-Entity-Ref-ID']);
        $this->assertStringContainsString($payment->checkout_url, view($content->text, compact('payment'))->render());
    }

    public function test_account_activation_email_contains_the_correct_details_for_every_offer(): void
    {
        $offers = [
            'start' => ['START', 'Jusqu’à 2 utilisateurs', 'Sauvegardes hebdomadaires'],
            'business' => ['BUSINESS', 'Jusqu’à 5 utilisateurs', 'Sauvegardes quotidiennes'],
            'premium' => ['PREMIUM', 'Utilisateurs illimités', 'Serveur dédié et isolé'],
        ];

        foreach ($offers as $package => [$label, $users, $infrastructure]) {
            $company = new Company([
                'name' => 'Entreprise Démonstration',
                'subdomain' => 'entreprise-'.$package,
                'package' => $package,
                'status' => 'pending',
            ]);

            $payment = new Payment([
                'reference' => 'PAY-26-0042',
                'company_name' => 'Entreprise Démonstration',
                'customer_name' => 'Awa Koné',
                'customer_email' => 'awa@example.com',
                'package' => $package,
                'amount' => 70800,
                'currency' => 'XOF',
                'description' => "Formation comptable incluse.\nImport initial convenu.",
            ]);

            $user = new User([
                'name' => 'Awa Koné',
                'email' => 'awa@example.com',
                'role' => User::ROLE_CLIENT,
            ]);

            $mail = new AccountInvitationMail(
                $user,
                'https://login.solutcloud.com/reset-password/test-token',
                $company,
                $payment,
            );
            $html = $mail->render();
            $text = view('emails.text.account_invitation', [
                'user' => $user,
                'company' => $company,
                'payment' => $payment,
                'offerDetails' => $mail->offerDetails,
                'resetUrl' => $mail->resetUrl,
            ])->render();

            foreach ([$html, $text] as $content) {
                $this->assertStringContainsString('SOLUTCLOUD '.$label, $content);
                $this->assertStringContainsString($users, $content);
                $this->assertStringContainsString($infrastructure, $content);
                $this->assertStringContainsString('Description / Notes additionnelles', $content);
                $this->assertStringContainsString('Formation comptable incluse.', $content);
                $this->assertStringContainsString('PAY-26-0042', $content);
                $this->assertStringContainsString('70 800 XOF', $content);
                $this->assertStringContainsString('awa@example.com', $content);
                $this->assertStringContainsString('https://login.solutcloud.com/reset-password/test-token', $content);
            }

            $this->assertStringContainsString('Activer mon compte', $html);
        }
    }
}
