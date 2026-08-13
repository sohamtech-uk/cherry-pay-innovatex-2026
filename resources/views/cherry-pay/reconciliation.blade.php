@extends('layouts.app')
@section('title', 'Reconciliation '.$invoice->invoice_number)
@section('content')
@php $reconciliation=$invoice->reconciliations->first(); $settlement=$reconciliation?->settlement; @endphp
<div class="container topbar"><div class="eyebrow">Reconciliation result</div><h1>{{ $invoice->invoice_number }}</h1></div>
<main class="container section" style="padding-top:8px"><div class="invoice-grid">
<section><div class="card"><div class="row"><div><div class="label">Invoice amount</div><div class="amount">${{ number_format($invoice->amount,2) }}</div></div><span class="status status-{{ $invoice->status }}">{{ $invoice->status }}</span></div>
@if($reconciliation)<div class="row"><div><div class="label">Settlement</div><div class="value">{{ $settlement->asset }} {{ number_format($settlement->amount,2) }} · {{ $settlement->network }}</div></div><span class="status status-{{ $settlement->status }}">{{ $settlement->status }}</span></div>
<div class="row"><div><div class="label">Match confidence</div><div class="confidence">{{ number_format($reconciliation->confidence_score*100,0) }}%</div></div><div><div class="label">Result</div><div class="value">{{ $reconciliation->status==='automatic'?'Automatically Reconciled':'Human Review Required' }}</div><p class="muted">{{ $reconciliation->match_reason }}</p></div></div>
<div class="label" style="margin-top:16px">Transaction evidence</div><div class="mono">{{ $settlement->transaction_hash }}</div>
@else<p>No confirmed reconciliation yet. <a href="{{ route('demo') }}">Return to demo.</a></p>@endif</div></section>
<aside class="card"><h3>Audit trail</h3><div class="timeline" style="margin-top:22px">@forelse($events as $event)<div class="event"><strong>{{ str_replace(['.','_'],' ',ucfirst($event->event_type)) }}</strong><time>{{ $event->created_at->format('H:i:s') }}</time></div>@empty<p class="muted">No events yet.</p>@endforelse</div></aside>
</div></main>
@endsection
