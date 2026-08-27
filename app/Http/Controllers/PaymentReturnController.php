<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentSynchronizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PaymentReturnController extends Controller
{
    public function __invoke(Request $request, PaymentSynchronizer $synchronizer): RedirectResponse
    {
        $monerooPaymentId = (string) $request->query('paymentId', '');
        $payment = $monerooPaymentId !== ''
            ? Payment::where('moneroo_payment_id', $monerooPaymentId)->first()
            : null;

        if ($payment === null) {
            return redirect()->route('login')->withErrors([
                'payment' => 'Le paiement retourné par Moneroo est introuvable.',
            ]);
        }

        try {
            if (! $payment->isPaid()) {
                $payment = $synchronizer->synchronize($payment);
            }
        } catch (Throwable $exception) {
            Log::error('MONEROO_PAYMENT_RETURN_FAILED', [
                'payment_id' => $payment->id,
                'message' => $exception->getMessage(),
            ]);

            return $this->unconfirmedRedirect($payment);
        }

        if (! $payment->isPaid()) {
            return $this->unconfirmedRedirect($payment);
        }

        if (in_array($payment->purpose, [Payment::PURPOSE_RENEWAL, Payment::PURPOSE_UPGRADE], true)) {
            return redirect()->route('client.dashboard')->with(
                'status',
                'Paiement confirmé. Votre abonnement a été mis à jour.',
            );
        }

        try {
            $user = $this->initialCustomer($payment);

            if ($user->password_initialized_at !== null) {
                $route = $user->company_id !== null ? 'client.dashboard' : 'login';

                return redirect()->route($route)->with(
                    'status',
                    'Votre espace client est déjà activé. Connectez-vous avec votre mot de passe.',
                );
            }

            $token = Password::createToken($user);

            return redirect()->route('password.reset', [
                'token' => $token,
                'email' => $user->email,
                'activation' => 1,
            ]);
        } catch (Throwable $exception) {
            Log::error('INITIAL_CUSTOMER_ACTIVATION_FAILED', [
                'payment_id' => $payment->id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'payment' => 'Le paiement est confirmé, mais l’activation automatique du compte a échoué. Contactez le support SOLUTCLOUD.',
            ]);
        }
    }

    private function initialCustomer(Payment $payment): User
    {
        $email = mb_strtolower(trim($payment->customer_email));
        $user = User::where('email', $email)->first();

        if ($user !== null) {
            if (! $user->isClient()) {
                throw new RuntimeException('Cette adresse e-mail appartient à un compte non client.');
            }

            return $user;
        }

        return User::create([
            'name' => $payment->customer_name,
            'email' => $email,
            'password' => Hash::make(Str::random(48)),
            'role' => User::ROLE_CLIENT,
            'company_id' => null,
        ]);
    }

    private function unconfirmedRedirect(Payment $payment): RedirectResponse
    {
        $route = in_array($payment->purpose, [Payment::PURPOSE_RENEWAL, Payment::PURPOSE_UPGRADE], true)
            ? 'client.renew'
            : 'login';

        return redirect()->route($route)->withErrors([
            'payment' => 'Le paiement n’a pas encore été confirmé. Vous pouvez réessayer depuis votre espace client ou contacter le support.',
        ]);
    }
}
