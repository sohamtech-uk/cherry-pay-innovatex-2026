<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cherry Pay — Intelligent Payments & Reconciliation">
    <title>@yield('title', 'Cherry Pay') · InnovateX 2026</title>
    <link rel="stylesheet" href="{{ asset('css/cherry-pay.css') }}">
</head>
<body>
<nav class="nav"><div class="container nav-inner">
    <a class="brand" href="{{ route('home') }}"><span class="brand-mark">C</span> Cherry Pay</a>
    <div class="nav-links">
        <a href="{{ route('home') }}">Overview</a><a href="{{ route('demo') }}">Invoices</a>
        <a href="{{ route('demo') }}#payments">Payments</a><a href="{{ route('reconciliation.index') }}">Reconciliation</a>
        <a href="{{ route('home') }}#architecture">Architecture</a>
    </div>
</div></nav>
@if(session('status'))<div class="container" style="padding-top:20px"><div class="notice">{{ session('status') }}</div></div>@endif
@yield('content')
<footer class="footer"><div class="container">NTU InnovateX 2026 · Hackathon prototype · Demo/testnet only · No real funds</div></footer>
</body>
</html>
