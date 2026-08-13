<?php

namespace Tests\Unit;

use App\Services\CherryPay\PaymentIntentService;
use App\Services\CherryPay\Settlement\DemoSettlementVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesDemoRecords;
use Tests\TestCase;

class DemoSettlementVerifierTest extends TestCase
{
    use CreatesDemoRecords, RefreshDatabase;

    public function test_demo_settlement_round_trip_is_confirmed(): void
    {
        $intent = app(PaymentIntentService::class)->createForInvoice($this->invoice());
        $verifier = app(DemoSettlementVerifier::class);
        $result = $verifier->verify($verifier->transactionHashFor($intent));

        $this->assertTrue($result->verified);
        $this->assertSame('250.00', $result->amount);
        $this->assertSame($intent->id, $result->metadata['intent_id']);
    }

    public function test_invalid_demo_settlement_is_rejected(): void
    {
        $result = app(DemoSettlementVerifier::class)->verify('0xnot-demo');

        $this->assertFalse($result->verified);
        $this->assertNotEmpty($result->failureReason);
    }
}
