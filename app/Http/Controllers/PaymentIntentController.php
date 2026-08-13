<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentIntent;
use App\Services\CherryPay\PaymentIntentService;
use App\Services\CherryPay\QrCodeService;
use Illuminate\Http\Request;

class PaymentIntentController extends Controller
{
    public function store(Request $request, PaymentIntentService $service)
    {
        $data = $request->validate(['invoice_id' => ['required', 'exists:invoices,id']]);
        $intent = $service->createForInvoice(Invoice::findOrFail($data['invoice_id']));

        return redirect()->route('pay.show', $intent->slug);
    }

    public function show(string $slug, PaymentIntentService $service, QrCodeService $qrCodes)
    {
        $intent = PaymentIntent::with(['invoice.merchant', 'settlements.reconciliation'])
            ->where('slug', $slug)
            ->firstOrFail();
        $intent = $service->markOpened($intent);
        $intent->load(['invoice.merchant', 'settlements.reconciliation']);
        $qrSvg = $qrCodes->svg($intent->qr_payload_url);

        return view('cherry-pay.pay', compact('intent', 'qrSvg'));
    }

    public function apiShow(PaymentIntent $intent)
    {
        return response()->json($intent->load(['invoice', 'settlements.reconciliation']));
    }
}
