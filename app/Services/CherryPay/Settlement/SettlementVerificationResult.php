<?php

namespace App\Services\CherryPay\Settlement;

final readonly class SettlementVerificationResult
{
    public function __construct(
        public bool $verified,
        public string $network,
        public string $asset,
        public string $transactionHash,
        public string $amount,
        public string $currency,
        public ?string $payerAddress = null,
        public ?int $blockNumber = null,
        public array $metadata = [],
        public ?string $failureReason = null,
    ) {}
}
