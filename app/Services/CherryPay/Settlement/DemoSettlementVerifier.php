<?php

namespace App\Services\CherryPay\Settlement;

use App\Models\PaymentIntent;
use Illuminate\Support\Str;

class DemoSettlementVerifier implements SettlementVerifier
{
    public function transactionHashFor(PaymentIntent $intent): string
    {
        $evidence = rtrim(strtr(base64_encode(json_encode([
            'intent' => $intent->id,
            'amount' => (string) $intent->amount,
            'currency' => $intent->currency,
            'reference' => $intent->reference,
        ], JSON_THROW_ON_ERROR)), '+/', '-_'), '=');

        return 'demo_'.$evidence.'_'.Str::lower(Str::random(12));
    }

    public function verify(string $transactionHash): SettlementVerificationResult
    {
        if (! str_starts_with($transactionHash, 'demo_')) {
            return $this->failed($transactionHash, 'Not a Cherry Pay demo settlement identifier.');
        }

        $parts = explode('_', $transactionHash, 3);
        if (count($parts) !== 3) {
            return $this->failed($transactionHash, 'Malformed demo settlement identifier.');
        }

        $encoded = strtr($parts[1], '-_', '+/');
        $encoded .= str_repeat('=', (4 - strlen($encoded) % 4) % 4);
        $json = base64_decode($encoded, true);
        $data = $json === false ? null : json_decode($json, true);
        if (! is_array($data) || empty($data['intent']) || ! is_numeric($data['amount'] ?? null)) {
            return $this->failed($transactionHash, 'Demo settlement evidence could not be decoded.');
        }

        return new SettlementVerificationResult(
            verified: true,
            network: 'EVM Testnet (simulated)',
            asset: 'USDC',
            transactionHash: $transactionHash,
            amount: number_format((float) $data['amount'], 2, '.', ''),
            currency: strtoupper((string) $data['currency']),
            payerAddress: '0xDEMO000000000000000000000000000000000001',
            blockNumber: 1042,
            metadata: ['intent_id' => $data['intent'], 'reference' => $data['reference'] ?? null, 'demo' => true],
        );
    }

    private function failed(string $transactionHash, string $reason): SettlementVerificationResult
    {
        return new SettlementVerificationResult(false, 'EVM Testnet (simulated)', 'USDC', $transactionHash, '0.00', 'USD', failureReason: $reason);
    }
}
