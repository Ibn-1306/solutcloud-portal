<?php

namespace App\Jobs;

use App\Mail\WebsiteLeadAcknowledgement;
use App\Mail\WebsiteLeadReceived;
use App\Models\WebsiteLead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendWebsiteLeadEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $leadId,
        public string $salesRecipient,
    ) {}

    public function handle(): void
    {
        $lead = WebsiteLead::find($this->leadId);

        if ($lead === null) {
            Log::warning('WEBSITE_LEAD_MAIL_SKIPPED', ['lead_id' => $this->leadId]);

            return;
        }

        $this->sendAcknowledgement($lead);
        $this->notifySales($lead);
    }

    private function sendAcknowledgement(WebsiteLead $lead): void
    {
        try {
            Mail::to($lead->email)->send(new WebsiteLeadAcknowledgement($lead));
            $lead->forceFill(['acknowledged_at' => now()])->save();
        } catch (Throwable $exception) {
            Log::error('WEBSITE_LEAD_ACKNOWLEDGEMENT_FAILED', [
                'lead_id' => $lead->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function notifySales(WebsiteLead $lead): void
    {
        try {
            Mail::to($this->salesRecipient)->send(new WebsiteLeadReceived($lead));
            $lead->forceFill(['notified_at' => now()])->save();
        } catch (Throwable $exception) {
            Log::error('WEBSITE_LEAD_MAIL_FAILED', [
                'lead_id' => $lead->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
