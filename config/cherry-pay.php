<?php

return [
    'demo_mode' => filter_var(env('DEMO_MODE', true), FILTER_VALIDATE_BOOLEAN),
    'intent_expiry_minutes' => 60,
    'auto_reconcile_threshold' => (float) env('AUTO_RECONCILE_THRESHOLD', 0.95),
    'evm' => [
        'rpc_url' => env('EVM_RPC_URL'),
        'chain_id' => env('EVM_CHAIN_ID'),
        'network_name' => env('EVM_NETWORK_NAME', 'EVM Testnet'),
        'usdc_contract_address' => env('USDC_CONTRACT_ADDRESS'),
        'merchant_wallet_address' => env('MERCHANT_WALLET_ADDRESS'),
    ],
];
