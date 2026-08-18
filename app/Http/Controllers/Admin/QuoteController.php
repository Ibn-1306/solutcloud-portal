<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\QuoteSendMail;
use App\Models\Quote;
use App\Services\MonerooPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    public function __construct(private readonly MonerooPaymentService $moneroo) {}

    public function index()
    {
        $quotes = Quote::latest()->get();

        return view('admin.quotes.index', compact('quotes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'company_name' => 'nullable|string|max:255',
            'amount' => 'required|integer|min:1',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        try {
            $quote = DB::transaction(function () use ($data): Quote {
                $quoteNumber = Quote::generateQuoteNumber();

                $quote = Quote::create([
                    'quote_number' => $quoteNumber,
                    'customer_name' => $data['customer_name'],
                    'customer_email' => $data['customer_email'],
                    'customer_phone' => $data['customer_phone'] ?? null,
                    'company_name' => $data['company_name'] ?? null,
                    'amount' => $data['amount'],
                    'duration' => $data['duration'],
                    'description' => $data['description'] ?? null,
                    'status' => Quote::STATUS_DRAFT,
                ]);

                $payment = $this->moneroo->initializeQuote($quote);

                $quote->update([
                    'payment_transaction_id' => $payment['id'],
                    'payment_url' => $payment['checkout_url'],
                    'payment_initialized_at' => now(),
                    'status' => Quote::STATUS_SENT,
                    'sent_at' => now(),
                ]);

                return $quote->fresh();
            });

            Mail::to($quote->customer_email)->send(new QuoteSendMail($quote));

            Log::info("QUOTE_CREATED_AND_SENT - {$quote->quote_number} - {$quote->customer_email}");

            return back()->with('status', "Devis {$quote->quote_number} généré et envoyé à {$quote->customer_email} avec succès.");

        } catch (\Exception $e) {
            Log::error('QUOTE_CREATE_FAILED : '.$e->getMessage());

            return back()->withErrors('Le devis n’a pas pu être généré avec son lien de paiement sécurisé. Vérifiez la configuration Moneroo puis réessayez.');
        }
    }
}
