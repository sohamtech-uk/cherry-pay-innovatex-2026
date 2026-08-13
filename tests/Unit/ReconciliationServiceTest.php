<?php

namespace Tests\Unit;

use App\Services\CherryPay\PaymentIntentService;
use App\Services\CherryPay\ReconciliationService;
use App\Services\CherryPay\Settlement\DemoSettlementVerifier;
use App\Services\CherryPay\Settlement\SettlementVerificationResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesDemoRecords;
use Tests\TestCase;

class ReconciliationServiceTest extends TestCase
{
    use CreatesDemoRecords, RefreshDatabase;

    public function test_matching_settlement_automatically_reconciles_invoice(): void
    {
        [$intent, $result] = $this->verified();
        $reconciliation = app(ReconciliationService::class)->reconcile($intent, $result);

        $this->assertSame('automatic', $reconciliation->status);
        $this->assertSame('paid', $intent->fresh()->invoice->status);
        $this->assertSame(1.0, $reconciliation->confidence_score);
    }

    public function test_wrong_amount_stays_in_review(): void
    {
        [$intent, $result] = $this->verified();
        $wrong = new SettlementVerificationResult(true, $result->network, 'USDC', 'demo_wrong_amount', '100.00', 'USD', metadata: ['intent_id' => $intent->id]);
        $reconciliation = app(ReconciliationService::class)->reconcile($intent, $wrong);

        $this->assertSame('suggested', $reconciliation->status);
        $this->assertSame('sent', $intent->fresh()->invoice->status);
    }

    public function test_duplicate_transaction_is_idempotent(): void
    {
        [$intent, $result] = $this->verified();
        $service = app(ReconciliationService::class);
        $first = $service->reconcile($intent, $result);
        $second = $service->reconcile($intent, $result);

        $this->assertTrue($first->is($second));
        $this->assertDatabaseCount('settlements', 1);
        $this->assertDatabaseCount('reconciliations', 1);
    }

    public function test_failed_settlement_is_rejected(): void
    {
        $intent = app(PaymentIntentService::class)->createForInvoice($this->invoice());
        $failed = new SettlementVerificationResult(false, 'EVM Testnet', 'USDC', 'demo_failed', '0.00', 'USD', failureReason: 'Receipt failed');

        $this->assertNull(app(ReconciliationService::class)->reconcile($intent, $failed));
        $this->assertDatabaseHas('settlements', ['status' => 'failed']);
        $this->assertDatabaseCount('reconciliations', 0);
    }

    private function verified(): array
    {
        $intent = app(PaymentIntentService::class)->createForInvoice($this->invoice());
        $verifier = app(DemoSettlementVerifier::class);

        return [$intent, $verifier->verify($verifier->transactionHashFor($intent))];
    }
}
