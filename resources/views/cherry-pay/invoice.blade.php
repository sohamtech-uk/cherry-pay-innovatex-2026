@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('content')
<div class="container topbar"><div class="eyebrow">Invoice</div><h1>{{ $invoice->invoice_number }}</h1></div>
<main class="container section" style="padding-top:8px"><div class="invoice-grid">
<section class="card"><div class="row"><div><div class="label">Merchant</div><div class="value">{{ $invoice->merchant->name }}</div></div><span class="status status-{{ $invoice->status }}">{{ $invoice->status }}</span></div>
<div class="row"><div><div class="label">Bill to</div><div class="value">{{ $invoice->customer_name }}</div></div><div><div class="label">Due date</div><div class="value">{{ $invoice->due_date?->format('d M Y') ?? 'On receipt' }}</div></div></div>
<div class="row"><div class="value">Prototype services</div><div class="amount">${{ number_format($invoice->amount,2) }}</div></div></section>
<aside class="card"><h3>Cherry Pay</h3><p class="muted">Keep invoice context attached from payment request through audit.</p>
@if($intent=$invoice->paymentIntents->first())<a class="btn btn-primary" href="{{ route('pay.show',$intent->slug) }}">Open payment request</a>
@else<form method="POST" action="{{ route('payment-intents.store') }}">@csrf<input type="hidden" name="invoice_id" value="{{ $invoice->id }}"><button class="btn btn-primary">Create Cherry Pay Request</button></form>@endif</aside>
</div></main>
@endsection
