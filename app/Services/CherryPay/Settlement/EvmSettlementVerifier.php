<?php

namespace App\Services\CherryPay\Settlement;

use GuzzleHttp\Client;

class EvmSettlementVerifier implements SettlementVerifier
{
    public function __construct(private readonly Client $client) {}

    public function verify(string $transactionHash): SettlementVerificationResult
    {
        $rpcUrl = config('cherry-pay.evm.rpc_url');
        if (! $rpcUrl) {
            return $this->unsupported($transactionHash, 'EVM_RPC_URL is not configured.');
        }

        $response = $this->client->post($rpcUrl, ['json' => [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'eth_getTransactionReceipt',
            'params' => [$transactionHash],
        ]]);
        $receipt = json_decode((string) $response->getBody(), true)['result'] ?? null;

        if (! $receipt || ($receipt['status'] ?? null) !== '0x1') {
            return $this->unsupported($transactionHash, 'Transaction receipt is missing or unsuccessful.');
        }

        // Deliberately fail closed until ERC-20 Transfer log decoding and expected
        // token/recipient checks are completed and independently reviewed.
        return $this->unsupported($transactionHash, 'Experimental adapter: ERC-20 recipient and amount verification is not yet enabled.');
    }

    private function unsupported(string $transactionHash, string $reason): SettlementVerificationResult
    {
        return new SettlementVerificationResult(
            false,
            (string) config('cherry-pay.evm.network_name', 'EVM Testnet'),
            'USDC',
            $transactionHash,
            '0.00',
            'USD',
            failureReason: $reason,
        );
    }
}
