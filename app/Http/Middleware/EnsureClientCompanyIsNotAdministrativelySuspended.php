<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientCompanyIsNotAdministrativelySuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $isSuspended = $user?->isClient()
            && $user->company_id !== null
            && Company::query()->whereKey($user->company_id)->where([
                'status' => 'suspended',
                'suspension_reason' => Company::SUSPENSION_ADMINISTRATIVE,
            ])->exists();

        if ($isSuspended) {
            return redirect()->route('account.suspended');
        }

        return $next($request);
    }
}
