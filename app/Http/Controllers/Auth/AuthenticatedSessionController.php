<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Affiche la vue de connexion unique.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Gère l'authentification et la redirection selon le rôle.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        /**
         * LOGIQUE DE REDIRECTION "SENIOR"
         * On sépare les flux : Admin vers Gestion, Client vers Portail.
         */
        
        // 1. Redirection pour l'ADMINISTRATEUR
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        // 2. Redirection pour le CLIENT
        // Note : On ne vérifie pas le statut ici (active/suspended). 
        // On laisse le PortalController le faire pour que le client 
        // puisse se connecter même s'il doit payer son renouvellement.
        if ($user->role === 'client') {
            return redirect()->intended(route('client.dashboard'));
        }

        // Cas par défaut (Sécurité)
        return redirect()->intended('/');
    }

    /**
     * Déconnexion de la session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}