<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionExpiredController extends Controller
{
    public function show(Request $request): View
    {
        $company = $this->companyFor($request);

        return view('subscription-expired', [
            'statusUrl' => $company
                ? route('subscription.expired.status', ['instance' => $this->hostname($company->instance_url)])
                : null,
            'renewUrl' => $company
                ? route('subscription.expired.renew', ['instance' => $this->hostname($company->instance_url)])
                : route('login'),
        ]);
    }

    public function renew(Request $request): RedirectResponse
    {
        $company = $this->companyFor($request);

        abort_if($company === null, 404);

        $user = $request->user();

        if ($user instanceof User && $user->isClient() && $user->company_id === $company->id) {
            return redirect()->route('client.renew');
        }

        if ($user !== null) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $request->session()->put('url.intended', route('subscription.expired.renew', [
            'instance' => $this->hostname($company->instance_url),
        ]));

        return redirect()->route('login')->with(
            'status',
            'Connectez-vous avec le compte client associé à cette instance pour renouveler votre abonnement.',
        );
    }

    public function status(Request $request): JsonResponse
    {
        $company = $this->companyFor($request);

        abort_if($company === null, 404);

        return response()
            ->json([
                'status' => $company->status,
                'redirect_url' => $company->status === 'active'
                    ? $company->instance_url
                    : null,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    private function companyFor(Request $request): ?Company
    {
        $hostname = $this->hostname((string) $request->query('instance', ''));

        if ($hostname === null) {
            return null;
        }

        $suffix = '.solutcloud.com';

        if (str_ends_with($hostname, $suffix)) {
            $subdomain = substr($hostname, 0, -strlen($suffix));

            if ($subdomain !== '' && ! str_contains($subdomain, '.')) {
                return Company::query()->where('subdomain', $subdomain)->first();
            }
        }

        return Company::query()->where('custom_domain', $hostname)->first();
    }

    private function hostname(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        // Compatibilité avec les anciennes redirections LWS où Apache a
        // transformé "https%3A%2F%2F" en "httpsAFF".
        $value = preg_replace('/^https?AFF/i', 'https://', $value) ?? $value;

        $url = str_contains($value, '://') ? $value : 'https://'.$value;
        $hostname = parse_url($url, PHP_URL_HOST);

        if (! is_string($hostname) || $hostname === '') {
            return null;
        }

        return strtolower(rtrim($hostname, '.'));
    }
}
