<?php

namespace App\Services\CherryPay;

use App\Models\Invoice;
use App\Models\PaymentIntent;
use App\Models\Settlement;

class ReconciliationMatcher
{
    /**
     * Deterministic settlement evidence is authoritative. AI may assist a human
     * with exceptions, but it must never override an uncertain money match.
     *
     * @return array{confidence: float, reason: string}
     */
    public function match(Settlement $settlement, PaymentIntent $intent, Invoice $invoice): array
    {
        $amountMatches = $this->money($settlement->amount) === $this->money($invoice->amount);
        $currencyMatches = strtoupper($settlement->currency) === strtoupper($invoice->currency);
        $intentMatches = $settlement->payment_intent_id === $intent->id && $intent->invoice_id === $invoice->id;
        $referenceMatches = strtoupper((string) ($settlement->metadata['reference'] ?? '')) === strtoupper($invoice->invoice_number);

        if ($intentMatches && $amountMatches && $currencyMatches) {
            return ['confidence' => 1.0, 'reason' => 'Payment intent, amount and currency match exactly.'];
        }

        if ($referenceMatches && $amountMatches && $currencyMatches) {
            return ['confidence' => 0.98, 'reason' => 'Invoice reference, amount and currency match exactly.'];
        }

        if ($currencyMatches && $this->money($settlement->amount) < $this->money($invoice->amount)) {
            return ['confidence' => 0.65, 'reason' => 'Partial payment detected; human review required.'];
        }

        if ($referenceMatches) {
            return ['confidence' => 0.55, 'reason' => 'Reference matches but amount or currency differs.'];
        }

        if ($amountMatches && $currencyMatches) {
            return ['confidence' => 0.50, 'reason' => 'Amount and currency match without authoritative intent evidence.'];
        }

        return ['confidence' => 0.10, 'reason' => 'Settlement evidence does not match the invoice.'];
    }

    private function money(string|float|int $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }
}
