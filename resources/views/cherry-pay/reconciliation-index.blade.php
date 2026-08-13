@extends('layouts.app')
@section('title', 'Reconciliation')
@section('content')
<div class="container topbar"><div class="eyebrow">Control centre</div><h1>Reconciliation</h1><p class="muted">High-confidence evidence automates. Exceptions remain visible.</p></div>
<main class="container section" style="padding-top:8px"><div class="card"><table class="table"><thead><tr><th>Invoice</th><th>Settlement</th><th>Confidence</th><th>Status</th></tr></thead><tbody>
@forelse($reconciliations as $item)<tr><td><a href="{{ route('reconciliation.show',$item->invoice) }}">{{ $item->invoice->invoice_number }}</a></td><td>{{ $item->settlement->asset }} · {{ Str::limit($item->settlement->transaction_hash,24) }}</td><td>{{ number_format($item->confidence_score*100,0) }}%</td><td><span class="status status-{{ $item->status }}">{{ $item->status }}</span></td></tr>
@empty<tr><td colspan="4">No settlements have been reconciled yet. <a href="{{ route('demo') }}">Start the demo.</a></td></tr>@endforelse
</tbody></table></div></main>
@endsection
