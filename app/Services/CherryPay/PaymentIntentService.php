<?php

namespace App\Services\CherryPay;

use App\Models\AuditEvent;
use App\Models\Invoice;
use App\Models\PaymentIntent;
use Illuminate\Support\Str;

class PaymentIntentService
{
    public function createForInvoice(Invoice $invoice): PaymentIntent
    {
        $existing = $invoice->paymentIntents()
            ->whereNotIn('status', ['paid', 'expired'])
            ->latest()
            ->first();

        if ($existing) {
            return $existing;
        }

        do {
            $slug = Str::lower(Str::random(14));
        } while (PaymentIntent::where('slug', $slug)->exists());

        $intent = PaymentIntent::create([
            'merchant_id' => $invoice->merchant_id,
            'invoice_id' => $invoice->id,
            'slug' => $slug,
            'reference' => $invoice->invoice_number,
            'amount' => $invoice->amount,
            'currency' => strtoupper($invoice->currency),
            'status' => 'created',
            'qr_payload_url' => route('pay.show', $slug),
            'expires_at' => now()->addMinutes((int) config('cherry-pay.intent_expiry_minutes')),
        ]);

        AuditEvent::create([
            'event_type' => 'payment_intent.created',
            'subject_type' => PaymentIntent::class,
            'subject_id' => $intent->id,
            'payload' => ['invoice_number' => $invoice->invoice_number, 'reference' => $intent->reference],
        ]);

        return $intent;
    }

    public function markOpened(PaymentIntent $intent): PaymentIntent
    {
        if ($intent->status === 'created') {
            $intent->update(['status' => 'opened']);
            AuditEvent::create([
                'event_type' => 'payment_intent.opened',
                'subject_type' => PaymentIntent::class,
                'subject_id' => $intent->id,
                'payload' => ['payment_url' => $intent->qr_payload_url],
            ]);
        }

        return $intent->refresh();
    }
}
