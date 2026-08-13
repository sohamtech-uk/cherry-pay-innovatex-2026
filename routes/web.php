<?php

use App\Http\Controllers\DemoController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentIntentController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\SettlementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DemoController::class, 'welcome'])->name('home');
Route::get('/demo', [DemoController::class, 'index'])->name('demo');

Route::post('/demo/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
Route::get('/demo/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

Route::post('/demo/payment-intents', [PaymentIntentController::class, 'store'])->name('payment-intents.store');
Route::get('/pay/{slug}', [PaymentIntentController::class, 'show'])->name('pay.show');

Route::post('/demo/settlements', [SettlementController::class, 'store'])->name('settlements.store');
Route::post('/demo/reconcile', [ReconciliationController::class, 'reconcile'])->name('reconcile.store');

Route::get('/demo/reconciliation', [ReconciliationController::class, 'index'])->name('reconciliation.index');
Route::get('/demo/reconciliation/{invoice}', [ReconciliationController::class, 'show'])->name('reconciliation.show');
