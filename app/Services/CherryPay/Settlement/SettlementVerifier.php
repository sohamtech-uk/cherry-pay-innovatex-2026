<?php

namespace App\Services\CherryPay\Settlement;

interface SettlementVerifier
{
    public function verify(string $transactionHash): SettlementVerificationResult;
}
