<?php

namespace App\Http\Controllers;

use App\Models\PaymentIntent;
use App\Services\CherryPay\ReconciliationService;
use App\Services\CherryPay\Settlement\DemoSettlementVerifier;
use App\Services\CherryPay\Settlement\SettlementVerifier;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function store(Request $request, SettlementVerifier $verifier, ReconciliationService $reconciliation)
    {
        $data = $request->validate([
            'payment_intent_id' => ['required', 'exists:payment_intents,id'],
            'transaction_hash' => ['nullable', 'string', 'max:500'],
        ]);
        $intent = PaymentIntent::findOrFail($data['payment_intent_id']);
        $transactionHash = $data['transaction_hash'] ?? null;

        if (! $transactionHash && $verifier instanceof DemoSettlementVerifier) {
            $transactionHash = $verifier->transactionHashFor($intent);
        }
        abort_if(! $transactionHash, 422, 'A transaction hash is required outside demo mode.');

        $result = $verifier->verify($transactionHash);
        $match = $reconciliation->reconcile($intent, $result);

        return redirect()
            ->route('reconciliation.show', $intent->invoice_id)
            ->with('status', $match?->status === 'automatic' ? 'Settlement verified and automatically reconciled.' : 'Settlement needs review.');
    }
}
