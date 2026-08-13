<?php

use App\Http\Controllers\PaymentIntentController;
use App\Http\Controllers\ReconciliationController;
use Illuminate\Support\Facades\Route;

Route::get('/payment-intents/{intent}', [PaymentIntentController::class, 'apiShow']);
Route::get('/reconciliations/{invoice}', [ReconciliationController::class, 'apiShow']);
