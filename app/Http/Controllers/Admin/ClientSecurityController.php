<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountInvitationMail;
use App\Models\ClientSecurityLink;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Throwable;

class ClientSecurityController extends Controller
{
    public function index(Request $request): View
    {
        $clientsQuery = User::query()
            ->where('role', User::ROLE_CLIENT)
            ->with('company')
            ->orderBy('name');
        $clients = (clone $clientsQuery)->get();
        $totalClientCount = $clients->count();
        $pendingActivationCount = $clients->whereNull('password_initialized_at')->count();
        $initializedClientCount = $totalClientCount - $pendingActivationCount;
        $sentLinkCount = ClientSecurityLink::query()->sent()->count();

        $links = ClientSecurityLink::query()
            ->with(['user.company', 'requester'])
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')->toString()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim($request->string('search')->toString());
                $query->whereHas('user', fn ($userQuery) => $userQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('company', fn ($companyQuery) => $companyQuery->where('name', 'like', "%{$search}%")));
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.client-security.index', compact(
            'clients',
            'links',
            'totalClientCount',
            'pendingActivationCount',
            'initializedClientCount',
            'sentLinkCount',
        ));
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_CLIENT)),
            ],
        ]);

        /** @var User $user */
        $user = User::query()->with(['company.payment'])->findOrFail($data['user_id']);
        $type = $user->password_initialized_at === null
            ? ClientSecurityLink::TYPE_ACTIVATION
            : ClientSecurityLink::TYPE_RESET;
        $tracking = ClientSecurityLink::create([
            'user_id' => $user->id,
            'requested_by' => $request->user()?->id,
            'type' => $type,
            'status' => ClientSecurityLink::STATUS_PENDING,
        ]);

        try {
            $token = Password::createToken($user);
            $resetUrl = url('/reset-password/'.$token.'?email='.urlencode($user->email));

            if ($type === ClientSecurityLink::TYPE_ACTIVATION) {
                abort_if($user->company === null, 422, 'Ce compte client n’est rattaché à aucune entreprise.');
                Mail::to($user->email)->send(new AccountInvitationMail(
                    $user,
                    $resetUrl,
                    $user->company,
                    $user->company->payment,
                ));
                $message = "Un nouveau lien d’activation a été envoyé à {$user->email}.";
            } else {
                $user->sendPasswordResetNotification($token);
                $message = "Un lien de réinitialisation du mot de passe a été envoyé à {$user->email}.";
            }

            $tracking->forceFill([
                'status' => ClientSecurityLink::STATUS_SENT,
                'sent_at' => now(),
                'failure_reason' => null,
            ])->save();

            return back()->with('status', $message);
        } catch (Throwable $exception) {
            $tracking->forceFill([
                'status' => ClientSecurityLink::STATUS_FAILED,
                'failure_reason' => $exception->getMessage(),
            ])->save();
            Log::error('CLIENT_SECURITY_LINK_FAILED', [
                'tracking_id' => $tracking->id,
                'user_id' => $user->id,
                'type' => $type,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return back()->withErrors('Le lien sécurisé n’a pas pu être envoyé. Consultez le suivi puis réessayez.');
        }
    }
}
