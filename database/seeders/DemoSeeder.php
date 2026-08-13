<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Merchant;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $merchant = Merchant::updateOrCreate(
            ['external_reference' => 'DEMO-MERCHANT-001'],
            ['name' => 'Acme Health Ltd', 'wallet_address' => null],
        );

        Invoice::updateOrCreate(
            ['invoice_number' => 'INV-1042'],
            [
                'merchant_id' => $merchant->id,
                'customer_name' => 'Global Ventures Ltd',
                'amount' => 250.00,
                'currency' => 'USD',
                'status' => 'sent',
                'due_date' => now()->addDays(14)->toDateString(),
            ],
        );
    }
}
