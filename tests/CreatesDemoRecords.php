<?php

namespace Tests;

use App\Models\Invoice;
use App\Models\Merchant;
use Illuminate\Support\Str;

trait CreatesDemoRecords
{
    protected function invoice(array $overrides = []): Invoice
    {
        $merchant = Merchant::create([
            'name' => 'Acme Health Ltd',
            'external_reference' => 'DEMO-'.Str::lower(Str::random(10)),
        ]);

        return Invoice::create(array_merge([
            'merchant_id' => $merchant->id,
            'invoice_number' => 'INV-'.random_int(1000, 9999).Str::lower(Str::random(4)),
            'customer_name' => 'Global Ventures Ltd',
            'amount' => 250.00,
            'currency' => 'USD',
            'status' => 'sent',
        ], $overrides));
    }
}
