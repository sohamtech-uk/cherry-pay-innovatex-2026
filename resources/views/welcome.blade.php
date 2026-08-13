@extends('layouts.app')
@section('title', 'Intelligent Payments & Reconciliation')
@section('content')
<main>
<section class="hero"><div class="container">
    <div class="eyebrow">NTU InnovateX 2026 · Payments & Financial Infrastructure</div>
    <h1>Payment is only the beginning.</h1>
    <p class="lead">Cherry Pay connects an invoice to independently verifiable settlement evidence, intelligent matching, automatic reconciliation and a clear audit trail.</p>
    <div class="flowline"><span>Invoice</span><b class="arrow">→</b><span>Pay</span><b class="arrow">→</b><span>Settle</span><b class="arrow">→</b><span>Reconcile</span></div>
    <div class="actions"><a class="btn btn-primary" href="{{ route('demo') }}">Launch live demo</a><a class="btn btn-secondary" href="#architecture">Explore architecture</a></div>
</div></section>
<section class="section"><div class="container">
    <div class="eyebrow">The problem</div><h2 class="section-title">Five disconnected steps. One missing truth.</h2>
    <p class="lead">Businesses often invoice, collect payment, verify settlement, reconcile and audit in separate systems. Cherry Pay makes the settlement event a reliable trigger for the workflow that follows.</p>
    <div class="grid" style="margin-top:34px">
        <div class="card"><h3>Payment intent</h3><p class="muted">A unique invoice reference, public link and scannable QR keep context attached to the payment.</p></div>
        <div class="card"><h3>Verified settlement</h3><p class="muted">A verifier abstraction accepts repeatable demo evidence today and isolates future EVM testnet verification.</p></div>
        <div class="card"><h3>Authoritative matching</h3><p class="muted">Exact intent, amount and currency evidence drives automation. Uncertain cases stay visible for review.</p></div>
    </div>
</div></section>
<section class="section section-soft" id="architecture"><div class="container">
    <div class="eyebrow">Architecture</div><h2 class="section-title">Deterministic at the money boundary.</h2>
    <div class="flowline"><span>Merchant</span><b>→</b><span>Invoice</span><b>→</b><span>Payment Intent + QR</span><b>→</b><span>Settlement Verifier</span><b>→</b><span>Matcher</span><b>→</b><span>Audit Trail</span></div>
    <p class="lead">AI can help explain exceptions later. It does not override uncertain evidence or decide that money arrived.</p>
</div></section>
</main>
@endsection
