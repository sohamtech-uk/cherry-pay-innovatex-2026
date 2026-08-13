<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\PaymentIntent;
use App\Models\Settlement;
use App\Services\CherryPay\ReconciliationMatcher;
use PHPUnit\Framework\TestCase;

class ReconciliationMatcherTest extends TestCase
{
    public function test_exact_intent_amount_and_currency_is_100_percent(): void
    {
        [$invoice, $intent, $settlement] = $this->records('250.00');
        $match = (new ReconciliationMatcher)->match($settlement, $intent, $invoice);

        $this->assertSame(1.0, $match['confidence']);
    }

    public function test_partial_payment_is_not_automatic(): void
    {
        [$invoice, $intent, $settlement] = $this->records('125.00');
        $match = (new ReconciliationMatcher)->match($settlement, $intent, $invoice);

        $this->assertLessThan(0.95, $match['confidence']);
        $this->assertStringContainsString('Partial', $match['reason']);
    }

    public function test_wrong_currency_is_not_automatic(): void
    {
        [$invoice, $intent, $settlement] = $this->records('250.00');
        $settlement->currency = 'GBP';
        $match = (new ReconciliationMatcher)->match($settlement, $intent, $invoice);

        $this->assertLessThan(0.95, $match['confidence']);
    }

    public function test_wrong_invoice_is_not_automatic(): void
    {
        [$invoice, $intent, $settlement] = $this->records('250.00');
        $intent->invoice_id = 99;
        $match = (new ReconciliationMatcher)->match($settlement, $intent, $invoice);

        $this->assertLessThan(0.95, $match['confidence']);
    }

    private function records(string $settled): array
    {
        $invoice = new Invoice(['id' => 42, 'invoice_number' => 'INV-1042', 'amount' => '250.00', 'currency' => 'USD']);
        $invoice->id = 42;
        $intent = new PaymentIntent(['amount' => '250.00', 'currency' => 'USD']);
        $intent->id = 'intent-1';
        $intent->invoice_id = 42;
        $settlement = new Settlement(['amount' => $settled, 'currency' => 'USD', 'metadata' => []]);
        $settlement->payment_intent_id = 'intent-1';

        return [$invoice, $intent, $settlement];
    }
}
