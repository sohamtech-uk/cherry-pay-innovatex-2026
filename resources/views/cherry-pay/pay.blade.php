@extends('layouts.app')
@section('title', 'Pay '.$intent->reference)
@section('content')
<div class="container topbar"><div class="eyebrow">Secure payment request</div><h1>{{ $intent->reference }}</h1></div>
<main class="container section" style="padding-top:8px">
<div class="notice">DEMO / TESTNET · NO REAL FUNDS</div>
<div class="steps"><div class="step active">Invoice ✓</div><div class="step active">Payment intent ✓</div><div class="step {{ $intent->settlements->isNotEmpty()?'active':'' }}">Settlement</div><div class="step {{ $intent->status==='paid'?'active':'' }}">Reconcile</div><div class="step {{ $intent->status==='paid'?'active':'' }}">Audit</div></div>
<div class="invoice-grid"><section class="card">
    <div class="row"><div><div class="label">Pay to</div><div class="value">{{ $intent->invoice->merchant->name }}</div></div><span class="status status-{{ $intent->status }}">{{ str_replace('_',' ',$intent->status) }}</span></div>
    <div class="row"><div><div class="label">For</div><div class="value">Invoice {{ $intent->reference }}</div></div><div class="amount">${{ number_format($intent->amount,2) }}</div></div>
    <div class="row"><div><div class="label">Currency / asset</div><div class="value">{{ $intent->currency }} / USDC demo</div></div><div><div class="label">Expires</div><div class="value">{{ $intent->expires_at->format('H:i, d M') }}</div></div></div>
    @if($intent->status!=='paid')<form method="POST" action="{{ route('settlements.store') }}">@csrf<input type="hidden" name="payment_intent_id" value="{{ $intent->id }}"><button class="btn btn-primary" style="width:100%;margin-top:18px">Simulate USDC Settlement</button></form>
    @else<a class="btn btn-primary" style="width:100%;margin-top:18px" href="{{ route('reconciliation.show',$intent->invoice_id) }}">View reconciliation & audit trail</a>@endif
</section>
<aside class="qr-wrap"><div class="label">Scan payment link</div>{!! $qrSvg !!}<div class="mono">{{ $intent->qr_payload_url }}</div><p class="muted">QR encodes the public payment URL. No wallet key is requested or stored.</p></aside></div>
</main>
@endsection
