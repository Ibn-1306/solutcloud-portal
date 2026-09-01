<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountSuspendedController extends Controller
{
    public function show(Request $request): Response
    {
        $company = $this->companyFor($request);

        return response()
            ->view('account-suspended', [
                'company' => $company,
                'statusUrl' => $company
                    ? route('account.suspended.status', ['instance' => $this->hostname($company->instance_url)])
                    : null,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function status(Request $request): JsonResponse
    {
        $company = $this->companyFor($request);

        abort_if($company === null, 404);

        $user = $request->user();
        $redirectUrl = null;

        if ($company->status === 'active') {
            $redirectUrl = $user instanceof User
                && $user->isClient()
                && $user->company_id === $company->id
                    ? route('client.dashboard')
                    : $company->instance_url;
        }

        return response()
            ->json([
                'status' => $company->status,
                'suspension_reason' => $company->suspension_reason,
                'package' => strtolower($company->package),
                'redirect_url' => $redirectUrl,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    private function companyFor(Request $request): ?Company
    {
        $user = $request->user();

        if ($user instanceof User && $user->isClient() && $user->company_id !== null) {
            return Company::query()->find($user->company_id);
        }

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

        $value = preg_replace('/^https?AFF/i', 'https://', $value) ?? $value;
        $url = str_contains($value, '://') ? $value : 'https://'.$value;
        $hostname = parse_url($url, PHP_URL_HOST);

        return is_string($hostname) && $hostname !== ''
            ? strtolower(rtrim($hostname, '.'))
            : null;
    }
}
