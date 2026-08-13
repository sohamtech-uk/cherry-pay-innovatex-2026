@extends('layouts.app')
@section('title', 'Demo')
@section('content')
<div class="container topbar"><div class="eyebrow">Live prototype</div><h1>Invoice to reconciliation</h1><p class="muted">Run the complete flow locally. Every person, account and settlement is fictional.</p></div>
<main class="container section" style="padding-top:12px">
<div class="notice">DEMO / TESTNET · NO REAL FUNDS</div>
<div class="steps"><div class="step active">1 · Invoice</div><div class="step">2 · Payment intent</div><div class="step">3 · Settlement</div><div class="step">4 · Reconcile</div><div class="step">5 · Audit</div></div>
<div class="invoice-grid" id="payments">
    <section>
    @forelse($invoices as $invoice)
        @php $intent=$invoice->paymentIntents->first(); $reconciliation=$intent?->settlements->first()?->reconciliation; @endphp
        <article class="card" style="margin-bottom:18px">
            <div class="row"><div><div class="label">Invoice</div><div class="value">{{ $invoice->invoice_number }}</div></div><span class="status status-{{ $invoice->status }}">{{ str_replace('_',' ',$invoice->status) }}</span></div>
            <div class="row"><div><div class="label">From</div><div class="value">{{ $invoice->merchant->name }}</div></div><div><div class="label">Customer</div><div class="value">{{ $invoice->customer_name }}</div></div></div>
            <div class="row"><div><div class="label">Amount due</div><div class="amount">${{ number_format($invoice->amount, 2) }}</div></div>
                <div class="actions">
                    <a class="btn btn-secondary" href="{{ route('invoices.show',$invoice) }}">View invoice</a>
                    @if(!$intent)<form method="POST" action="{{ route('payment-intents.store') }}">@csrf<input type="hidden" name="invoice_id" value="{{ $invoice->id }}"><button class="btn btn-primary">Create Cherry Pay Request</button></form>
                    @else<a class="btn btn-primary" href="{{ route('pay.show',$intent->slug) }}">{{ $reconciliation ? 'View payment' : 'Continue payment' }}</a>@endif
                </div>
            </div>
        </article>
    @empty<div class="card"><p>No demo invoice yet. Create one using the form.</p></div>@endforelse
    </section>
    <aside class="card"><h3>Create another demo invoice</h3><p class="muted">Fictional data only.</p>
        <form method="POST" action="{{ route('invoices.store') }}" class="form-grid">@csrf
            <div class="field full"><label>Invoice number</label><input name="invoice_number" value="INV-{{ random_int(2000,9999) }}" required></div>
            <div class="field full"><label>Customer</label><input name="customer_name" value="Demo Customer" required></div>
            <div class="field"><label>Amount</label><input name="amount" type="number" step="0.01" value="125.00" required></div>
            <div class="field"><label>Currency</label><input name="currency" value="USD" maxlength="3" required></div>
            @if($errors->any())<div class="error full">{{ $errors->first() }}</div>@endif
            <button class="btn btn-secondary full">Create invoice</button>
        </form>
    </aside>
</div></main>
@endsection
