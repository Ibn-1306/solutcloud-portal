<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Demo;
use App\Mail\DemoAccessMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Mail};

class DemoController extends Controller
{
    public function index()
    {
        $demos = Demo::latest()->get();
        return view('admin.demos.index', compact('demos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'subdomain'    => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:30',
            'erp_login'    => 'required|string|max:255',
            'erp_password' => 'required|string|max:255',
        ]);

        try {
            $demo = Demo::create([
                'company_name' => $data['company_name'],
                'subdomain'     => strtolower($data['subdomain']),
                'email'         => $data['email'],
                'phone'         => $data['phone'] ?? null,
                'erp_login'     => $data['erp_login'],
                'erp_password'  => $data['erp_password'],
            ]);

            Mail::to($demo->email)->send(new DemoAccessMail($demo));

            Log::info("DEMO_CREATED - {$demo->company_name} - {$demo->email}");

            return back()->with('status', "Accès de démonstration envoyé à {$demo->email}.");

        } catch (\Exception $e) {
            Log::error("DEMO_CREATE_FAILED : " . $e->getMessage());
            return back()->withErrors("Erreur lors de l'envoi de la démonstration : " . $e->getMessage());
        }
    }
}