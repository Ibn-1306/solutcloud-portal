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
     * Affiche la vue de connexion.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Authentifie l'utilisateur et redirige selon son rôle.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Régénération de session après authentification.
        $request->session()->regenerate();

        $user = Auth::user();

        // Administrateur : accès à l'espace de gestion.
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Client : accès au portail client.
        // Les contrôles métier restent gérés par le portail.
        if ($user->role === 'client') {
            return redirect()->intended(route('client.dashboard'));
        }

        // Sécurité : rôle non reconnu.
        return redirect()->intended('/');
    }

    /**
     * Déconnecte l'utilisateur et détruit sa session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}