<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Merchant;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => ['required', 'string', 'max:50', 'unique:invoices'],
            'customer_name' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
        ]);
        $merchant = Merchant::firstOrCreate(
            ['external_reference' => 'DEMO-MERCHANT-001'],
            ['name' => 'Acme Health Ltd'],
        );
        $invoice = $merchant->invoices()->create(array_merge($data, [
            'currency' => strtoupper($data['currency']),
            'status' => 'sent',
        ]));

        return redirect()->route('invoices.show', $invoice);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['merchant', 'paymentIntents.settlements.reconciliation']);

        return view('cherry-pay.invoice', compact('invoice'));
    }
}
