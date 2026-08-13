<?php

namespace App\Services\CherryPay;

use App\Models\AuditEvent;
use App\Models\PaymentIntent;
use App\Models\Reconciliation;
use App\Models\Settlement;
use App\Services\CherryPay\Settlement\SettlementVerificationResult;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReconciliationService
{
    public function __construct(private readonly ReconciliationMatcher $matcher) {}

    public function reconcile(PaymentIntent $intent, SettlementVerificationResult $verification): ?Reconciliation
    {
        return DB::transaction(function () use ($intent, $verification) {
            $duplicate = Settlement::where('transaction_hash', $verification->transactionHash)->first();
            if ($duplicate) {
                if ($duplicate->payment_intent_id !== $intent->id) {
                    throw new RuntimeException('This transaction is already assigned to another payment intent.');
                }

                return $duplicate->reconciliation;
            }

            $settlement = Settlement::create([
                'payment_intent_id' => $intent->id,
                'network' => $verification->network,
                'asset' => $verification->asset,
                'transaction_hash' => $verification->transactionHash,
                'payer_address' => $verification->payerAddress,
                'amount' => $verification->amount,
                'currency' => $verification->currency,
                'status' => $verification->verified ? 'confirmed' : 'failed',
                'block_number' => $verification->blockNumber,
                'confirmed_at' => $verification->verified ? now() : null,
                'metadata' => array_merge($verification->metadata, ['failure_reason' => $verification->failureReason]),
            ]);

            $this->audit('settlement.'.($verification->verified ? 'confirmed' : 'failed'), $settlement, [
                'transaction_hash' => $verification->transactionHash,
                'reason' => $verification->failureReason,
            ]);

            if (! $verification->verified) {
                return null;
            }

            $invoice = $intent->invoice;
            if (! $invoice) {
                throw new RuntimeException('The payment intent is not linked to an invoice.');
            }

            $match = $this->matcher->match($settlement, $intent, $invoice);
            $automatic = $match['confidence'] >= (float) config('cherry-pay.auto_reconcile_threshold');

            $reconciliation = Reconciliation::create([
                'payment_intent_id' => $intent->id,
                'invoice_id' => $invoice->id,
                'settlement_id' => $settlement->id,
                'confidence_score' => $match['confidence'],
                'match_reason' => $match['reason'],
                'status' => $automatic ? 'automatic' : 'suggested',
                'reconciled_at' => $automatic ? now() : null,
            ]);

            $this->audit('reconciliation.match_found', $reconciliation, $match);

            if ($automatic) {
                $invoice->update(['status' => 'paid']);
                $intent->update(['status' => 'paid']);
                $this->audit('invoice.reconciled_automatically', $invoice, [
                    'reconciliation_id' => $reconciliation->id,
                    'confidence' => $match['confidence'],
                ]);
            } else {
                $intent->update(['status' => 'settlement_pending']);
                $this->audit('reconciliation.review_required', $reconciliation, $match);
            }

            return $reconciliation->fresh();
        });
    }

    private function audit(string $type, object $subject, array $payload): void
    {
        AuditEvent::create([
            'event_type' => $type,
            'subject_type' => $subject::class,
            'subject_id' => (string) $subject->id,
            'payload' => $payload,
        ]);
    }
}
