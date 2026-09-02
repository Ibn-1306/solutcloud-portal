<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendWebsiteLeadEmails;
use App\Models\WebsiteLead;
use App\Rules\InternationalPhoneNumber;
use App\Rules\UniqueCustomerEmail;
use App\Support\InternationalPhone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class WebsiteLeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        $accountRequest = in_array($request->input('type'), ['order', 'quote'], true);
        if ($accountRequest && WebsiteLead::query()
            ->whereIn('type', ['order', 'quote'])
            ->whereRaw('LOWER(email) = ?', [$request->input('email')])
            ->exists()) {
            return response()->json([
                'error' => 'Un compte SOLUTCLOUD existe déjà avec cette adresse e-mail. Veuillez choisir une autre adresse e-mail.',
                'field' => 'email',
            ], 422);
        }

        $data = $request->validate([
            'type' => ['required', 'string', Rule::in(['contact', 'trial', 'order', 'quote'])],
            'fullname' => ['required', 'string', 'max:255', 'not_regex:/[\r\n]/'],
            'email' => array_filter([
                'required',
                'email:rfc',
                'max:255',
                $accountRequest ? new UniqueCustomerEmail : null,
            ]),
            'phone' => ['nullable', 'required_if:type,trial,order,quote', 'string', 'max:30', new InternationalPhoneNumber],
            'company_name' => ['nullable', 'required_if:type,trial,order,quote', 'string', 'max:255'],
            'profile' => ['nullable', 'string', 'max:100'],
            'offer' => ['nullable', 'string', Rule::in(['START', 'BUSINESS', 'PREMIUM'])],
            'message' => ['nullable', 'required_if:type,contact,trial', 'string', 'max:5000'],
        ]);

        if ($data['type'] === 'order' && ! in_array($data['offer'] ?? null, ['START', 'BUSINESS'], true)) {
            return response()->json([
                'error' => 'Une offre START ou BUSINESS est requise pour commander.',
            ], 422);
        }

        if ($data['type'] === 'quote' && ($data['offer'] ?? null) !== 'PREMIUM') {
            return response()->json([
                'error' => 'L’offre PREMIUM est requise pour demander un devis.',
            ], 422);
        }

        $data['email'] = mb_strtolower(trim($data['email']));
        $data['phone'] = InternationalPhone::normalize($data['phone'] ?? null);
        $recipient = (string) config('services.solutcloud.contact_recipient', 'sales@i-solutions.ci');

        if (filter_var($recipient, FILTER_VALIDATE_EMAIL) === false) {
            Log::critical('WEBSITE_LEAD_RECIPIENT_INVALID');

            return response()->json([
                'error' => 'Le service de contact est temporairement indisponible.',
            ], 503);
        }

        $lead = WebsiteLead::create($data);

        SendWebsiteLeadEmails::dispatch($lead->id, $recipient)
            ->onConnection('deferred');

        return response()->json([
            'status' => 'success',
            'message' => 'Demande transmise.',
        ], 201);
    }
}
