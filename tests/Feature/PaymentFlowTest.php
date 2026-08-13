<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\PaymentIntent;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_to_settlement_to_reconciliation_flow(): void
    {
        $this->seed(DemoSeeder::class);
        $this->get('/demo')->assertOk()->assertSee('INV-1042');

        $response = $this->post('/demo/payment-intents', ['invoice_id' => 1]);
        $intent = PaymentIntent::firstOrFail();
        $response->assertRedirect(route('pay.show', $intent->slug));

        $this->get(route('pay.show', $intent->slug))->assertOk()->assertSee('Simulate USDC Settlement');
        $this->post('/demo/settlements', ['payment_intent_id' => $intent->id])
            ->assertRedirect(route('reconciliation.show', 1));

        $this->assertDatabaseHas('invoices', ['id' => 1, 'status' => 'paid']);
        $this->assertDatabaseHas('reconciliations', ['invoice_id' => 1, 'status' => 'automatic']);
        $this->assertGreaterThanOrEqual(4, AuditEvent::count());
        $this->get('/demo/reconciliation/1')->assertOk()->assertSee('100%')->assertSee('Automatically Reconciled');
    }

    public function test_api_exposes_intent_and_reconciliation_evidence(): void
    {
        $this->seed(DemoSeeder::class);
        $this->post('/demo/payment-intents', ['invoice_id' => 1]);
        $intent = PaymentIntent::firstOrFail();
        $this->post('/demo/settlements', ['payment_intent_id' => $intent->id]);

        $this->getJson('/api/payment-intents/'.$intent->id)->assertOk()->assertJsonPath('status', 'paid');
        $this->getJson('/api/reconciliations/1')->assertOk()->assertJsonPath('status', 'paid');
    }
}
