<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\WebsiteLeadAcknowledgement;
use App\Mail\WebsiteLeadReceived;
use App\Models\WebsiteLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class WebsiteLeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', Rule::in(['contact', 'trial', 'quote'])],
            'fullname' => ['required', 'string', 'max:255', 'not_regex:/[\r\n]/'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'required_if:type,trial,quote', 'string', 'max:30'],
            'company_name' => ['nullable', 'required_if:type,trial,quote', 'string', 'max:255'],
            'profile' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $data['email'] = mb_strtolower(trim($data['email']));
        $lead = WebsiteLead::create($data);
        $recipient = (string) config('services.solutcloud.contact_recipient', 'sales@i-solutions.ci');

        if (filter_var($recipient, FILTER_VALIDATE_EMAIL) === false) {
            Log::critical('WEBSITE_LEAD_RECIPIENT_INVALID', ['lead_id' => $lead->id]);

            return response()->json([
                'error' => 'Le service de contact est temporairement indisponible.',
            ], 503);
        }

        try {
            Mail::to($recipient)->send(new WebsiteLeadReceived($lead));
            $lead->forceFill(['notified_at' => now()])->save();
        } catch (Throwable $exception) {
            Log::error('WEBSITE_LEAD_MAIL_FAILED', [
                'lead_id' => $lead->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'error' => 'La demande a été enregistrée, mais la notification n’a pas pu être envoyée.',
            ], 502);
        }

        try {
            Mail::to($lead->email)->send(new WebsiteLeadAcknowledgement($lead));
            $lead->forceFill(['acknowledged_at' => now()])->save();
        } catch (Throwable $exception) {
            Log::warning('WEBSITE_LEAD_ACKNOWLEDGEMENT_FAILED', [
                'lead_id' => $lead->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Demande transmise.',
        ], 201);
    }
}
