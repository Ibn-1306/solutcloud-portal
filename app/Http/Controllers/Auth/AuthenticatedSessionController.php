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
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // 1. Redirection pour l'ADMINISTRATEUR (Gestion centrale)
        if ($user->role === 'admin') {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // 2. Logique pour le CLIENT
        // On récupère l'entreprise liée à l'utilisateur
        $company = $user->company; 

        // Vérification si l'entreprise existe et si elle est active
        if (!$company || $company->status !== 'active') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Votre accès est suspendu ou l\'instance est introuvable. Veuillez contacter le support SOLUTCLOUD.',
            ]);
        }

        // 3. Construction de l'URL de redirection vers son Dolibarr
        // Si Premium : domaine dédié. Sinon : sous-domaine solutcloud.com
        $url = ($company->package === 'premium') 
            ? "https://{$company->subdomain}" 
            : "https://{$company->subdomain}.solutcloud.com";

        // Redirection vers l'instance externe
        return redirect()->away($url);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}