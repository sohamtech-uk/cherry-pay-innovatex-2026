<?php

namespace Tests\Unit;

use App\Services\CherryPay\PaymentIntentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesDemoRecords;
use Tests\TestCase;

class PaymentIntentServiceTest extends TestCase
{
    use CreatesDemoRecords, RefreshDatabase;

    public function test_invoice_creates_a_unique_correct_payment_intent(): void
    {
        $invoice = $this->invoice();
        $intent = app(PaymentIntentService::class)->createForInvoice($invoice);

        $this->assertNotEmpty($intent->id);
        $this->assertSame(14, strlen($intent->slug));
        $this->assertSame('250.00', $intent->amount);
        $this->assertSame($invoice->invoice_number, $intent->reference);
        $this->assertSame('USD', $intent->currency);
        $this->assertStringContainsString('/pay/'.$intent->slug, $intent->qr_payload_url);
    }

    public function test_active_intent_creation_is_idempotent(): void
    {
        $invoice = $this->invoice();
        $service = app(PaymentIntentService::class);

        $this->assertTrue($service->createForInvoice($invoice)->is($service->createForInvoice($invoice)));
        $this->assertDatabaseCount('payment_intents', 1);
    }
}
