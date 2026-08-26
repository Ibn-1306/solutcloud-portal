<?php

namespace App\Http\Controllers;

use App\Jobs\VerifyMonerooPayment;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PaymentReturnController extends Controller
{
    public function __invoke(Request $request): View
    {
        $monerooPaymentId = (string) $request->query('paymentId', '');
        $payment = $monerooPaymentId !== ''
            ? Payment::where('moneroo_payment_id', $monerooPaymentId)->first()
            : null;

        if ($payment !== null && ! $payment->isPaid()) {
            VerifyMonerooPayment::dispatch($payment->id)->onConnection('deferred');
        }

        return view('payments.return', compact('payment'));
    }
}
