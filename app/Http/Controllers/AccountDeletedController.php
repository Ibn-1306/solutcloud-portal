<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class AccountDeletedController extends Controller
{
    public function __invoke(): Response
    {
        return response()
            ->view('account-deleted')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
