<?php

namespace App\Jobs;

use App\Mail\InstanceReadyMail;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendInstanceReadyEmail implements ShouldBeEncrypted, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<int, array{key: string, label: string, login: string, password: string}>  $credentials
     */
    public function __construct(
        public int $companyId,
        public array $credentials,
    ) {}

    public function handle(): void
    {
        $company = Company::with('users')->find($this->companyId);
        $user = $company?->users->first();

        if ($company === null || $user === null) {
            return;
        }

        try {
            Mail::to($user->email)->send(new InstanceReadyMail(
                $company,
                $company->instance_url,
                $this->credentials,
            ));
        } catch (Throwable $exception) {
            Log::error('INSTANCE_READY_MAIL_FAILED', [
                'company_id' => $this->companyId,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
