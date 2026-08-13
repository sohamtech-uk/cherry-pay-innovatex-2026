<?php

namespace App\Http\Controllers;

use App\Models\AuditEvent;
use App\Models\Invoice;
use App\Models\Reconciliation;
use Illuminate\Http\Request;

class ReconciliationController extends Controller
{
    public function index()
    {
        $reconciliations = Reconciliation::with(['invoice', 'settlement'])->latest()->get();

        return view('cherry-pay.reconciliation-index', compact('reconciliations'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['merchant', 'paymentIntents.settlements.reconciliation', 'reconciliations.settlement']);
        $ids = collect([$invoice->id])
            ->merge($invoice->paymentIntents->pluck('id'))
            ->merge($invoice->paymentIntents->flatMap->settlements->pluck('id'))
            ->merge($invoice->reconciliations->pluck('id'))
            ->map(fn ($id) => (string) $id);
        $events = AuditEvent::whereIn('subject_id', $ids)->orderBy('created_at')->get();

        return view('cherry-pay.reconciliation', compact('invoice', 'events'));
    }

    public function reconcile(Request $request)
    {
        $data = $request->validate(['invoice_id' => ['required', 'exists:invoices,id']]);

        return redirect()->route('reconciliation.show', $data['invoice_id']);
    }

    public function apiShow(Invoice $invoice)
    {
        return response()->json($invoice->load(['paymentIntents.settlements.reconciliation', 'reconciliations']));
    }
}
