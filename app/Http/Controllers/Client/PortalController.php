<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Support\OfferCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $company = $user->company()
            ->with('payment')
            ->first();

        if ($company === null) {
            Auth::logout();
            abort(403, 'Compte client sans entreprise.');
        }

        return view('client.dashboard', [
            'company' => $company,
            'payment' => $company->payment,
            'offerDetails' => OfferCatalog::details($company->package),
        ]);
    }

    public function profile(): View
    {
        $company = Auth::user()->company;

        abort_if($company === null, 403, 'Compte client sans entreprise.');

        return view('client.profile', compact('company'));
    }
}
