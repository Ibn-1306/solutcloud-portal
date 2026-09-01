<?php

namespace App\Jobs;

use App\Mail\InstanceInstallationMail;
use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Throwable;

class SendInstanceSetupEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $companyId, public int $userId) {}

    public function handle(): void
    {
        $company = Company::with('payment')->find($this->companyId);
        $user = User::find($this->userId);

        if ($company === null || $user === null) {
            Log::warning('INSTANCE_SETUP_MAIL_SKIPPED', [
                'company_id' => $this->companyId,
                'user_id' => $this->userId,
            ]);

            return;
        }

        try {
            $activationUrl = null;

            if ($user->password_initialized_at === null) {
                $token = Password::createToken($user);
                $activationUrl = url('/reset-password/'.$token.'?email='.urlencode($user->email).'&activation=1');
            }

            Mail::to($user->email)->send(new InstanceInstallationMail(
                $user,
                $company,
                $company->payment,
                $activationUrl,
            ));
        } catch (Throwable $exception) {
            Log::error('INSTANCE_INSTALLATION_MAIL_FAILED', [
                'company_id' => $company->id,
                'user_id' => $user->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
