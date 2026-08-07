<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    /**
     * Affiche le dashboard client
     */
    public function index()
    {
        // Utilisation de la relation directe définie dans le modèle User
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Compte orphelin. Contactez le support.');
        }

        return view('client.dashboard', compact('company'));
    }

    /**
     * Logique de renouvellement (SP3)
     */
    public function renew()
    {
        // Futur branchement sur l'OrderController pour Moneroo
        return back()->with('status', 'La passerelle de réabonnement est en cours de déploiement.');
    }
}