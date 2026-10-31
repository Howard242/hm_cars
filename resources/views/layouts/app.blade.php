<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'Find your next car with HM CARS. We connect buyers and sellers across Kenya.')">
    <title>@yield('title', 'HM CARS') — The Right Car. The Right Connection.</title>
    <link rel="stylesheet" href="{{ asset('css/hmcars.css') }}">
    <style>
        @media (max-width: 850px) {
            .nav-wrap { flex-wrap: wrap; justify-content: center; padding-block: 12px; }
            .site-header nav { justify-content: center; flex-wrap: wrap; gap: 12px 18px; }
        }
    </style>
    <script src="{{ asset('js/hmcars.js') }}" defer></script>
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark">HM</span><span>HM CARS<small>THE RIGHT CAR. THE RIGHT CONNECTION.</small></span></a>
        <nav aria-label="Main navigation">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('vehicles.index') }}">Browse cars</a>
            <a href="{{ route('ownership-guide.show') }}">How to own a car</a>
            <a class="nav-sell" href="{{ route('sell.create') }}">Sell With Us</a>
            <a class="nav-cta" href="#contact-hm">Contact HM CARS</a>
        </nav>
    </div>
</header>
<main>
    @if (session('success'))
        <div class="container"><div class="notice success" role="status">{{ session('success') }}</div></div>
    @endif
    @if ($errors->any())
        <div class="container"><div class="notice error" role="alert"><strong>Please check the form:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
    @endif
    @yield('content')
</main>
<footer class="site-footer" id="contact-hm">
    <div class="container footer-grid">
        <div><a class="brand brand-light" href="{{ route('home') }}"><span class="brand-mark">HM</span><span>HM CARS<small>THE RIGHT CAR. THE RIGHT CONNECTION.</small></span></a><p>Your car search, guided by a real team. We help coordinate the conversation before connecting buyers with sellers.</p></div>
        <div><h3>Explore</h3><a href="{{ route('vehicles.index') }}">Browse cars</a><a href="{{ route('home') }}#how-it-works">How it works</a><a href="{{ route('ownership-guide.show') }}">How to own a car</a></div>
        <div><h3>Talk to our team</h3>
            @if (config('hmcars.phone'))
                <a href="tel:+{{ preg_replace('/\D+/', '', config('hmcars.phone')) }}">Call HM CARS</a>
            @else
                <span class="footer-muted">Official phone number coming soon</span>
            @endif
            @if (config('hmcars.whatsapp'))
                <a target="_blank" rel="noopener noreferrer" href="https://wa.me/{{ preg_replace('/\D+/', '', config('hmcars.whatsapp')) }}?text={{ rawurlencode(isset($vehicle) ? 'Hello HM CARS, I am interested in the ' . $vehicle->year . ' ' . $vehicle->make . ' ' . $vehicle->model . ' (listing HMC-' . str_pad((string) $vehicle->id, 4, '0', STR_PAD_LEFT) . '). Please help me with the next steps.' : 'Hello HM CARS, I would like help finding a vehicle.') }}">WhatsApp HM CARS</a>
            @else
                <span class="footer-muted">WhatsApp contact coming soon</span>
            @endif
            <a href="{{ route('home') }}#inquiry-form">Send an inquiry online</a>
        </div>
    </div>
    <div class="container copyright">© {{ date('Y') }} HM CARS. All rights reserved. <span>Seller contact details are shared only through HM CARS coordination.</span></div>
</footer>
</body>
</html>
