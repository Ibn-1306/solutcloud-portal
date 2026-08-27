<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
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
        ]);
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

        $url = str_contains($value, '://') ? $value : 'https://'.$value;
        $hostname = parse_url($url, PHP_URL_HOST);

        if (! is_string($hostname) || $hostname === '') {
            return null;
        }

        return strtolower(rtrim($hostname, '.'));
    }
}
