<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

class DemoController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

    public function index()
    {
        $invoices = Invoice::with(['merchant', 'paymentIntents.settlements.reconciliation'])
            ->latest()
            ->get();

        return view('cherry-pay.demo', compact('invoices'));
    }
}
