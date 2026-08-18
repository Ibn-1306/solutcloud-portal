<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterWelcome;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NewsletterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
        ]);

        $email = mb_strtolower(trim($data['email']));
        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $email],
            ['is_active' => true, 'subscribed_at' => now()],
        );

        if (! $subscriber->is_active) {
            $subscriber->forceFill([
                'is_active' => true,
                'subscribed_at' => now(),
                'welcome_sent_at' => null,
            ])->save();
        }

        if ($subscriber->welcome_sent_at === null) {
            try {
                Mail::to($subscriber->email)->send(new NewsletterWelcome($subscriber));
                $subscriber->forceFill(['welcome_sent_at' => now()])->save();
            } catch (Throwable $exception) {
                Log::error('NEWSLETTER_WELCOME_MAIL_FAILED', [
                    'subscriber_id' => $subscriber->id,
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ]);

                return response()->json([
                    'error' => 'L’inscription est enregistrée, mais l’e-mail de confirmation n’a pas pu être envoyé.',
                ], 502);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Inscription enregistrée.',
        ], $subscriber->wasRecentlyCreated ? 201 : 200);
    }
}
