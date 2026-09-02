<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function updatePassword(Request $request)
    {

        $request->validate([

            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                'confirmed',
                'string', 'max:255',
            ],

        ]);

        $request->user()->update([

            'password' => Hash::make($request->password),

        ]);

        return back()->with(
            'status',
            'Votre mot de passe a été modifié avec succès.'
        );

    }
}
