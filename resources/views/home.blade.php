@extends('layouts.app')

@section('title', 'Find your next car')

@section('content')
<section class="hero hero-carousel" aria-label="Featured HM CARS messages">
    <style>
        .hero-carousel{position:relative;overflow:hidden}.hero-slide{display:none;min-height:520px}.hero-slide.is-active{display:grid;animation:hmHeroFade .65s ease-out}.hero-slide[style*="background-image"]{background-size:cover;background-position:center}.hero-slide[style*="background-image"] .hero-grid{position:relative}.hero-slide[style*="background-image"] .hero-grid:after{content:"";position:absolute;inset:0;z-index:-1;border-radius:18px;background:linear-gradient(90deg,rgba(251,250,247,.96) 0%,rgba(251,250,247,.78) 48%,rgba(251,250,247,.2) 100%)}.hero-carousel-controls{position:absolute;z-index:5;left:50%;bottom:24px;display:flex;gap:8px;transform:translateX(-50%)}.hero-dot{width:10px;height:10px;padding:0;border:2px solid white;border-radius:50%;background:rgba(23,107,77,.35);box-shadow:0 1px 8px rgba(0,0,0,.18);cursor:pointer}.hero-dot.is-active{width:34px;border-radius:20px;background:#176b4d}.hero-copy h1 em{display:block}.hero-copy h1{max-width:760px}.hero-copy p{max-width:650px}.hero-actions{flex-wrap:wrap}@keyframes hmHeroFade{from{opacity:.25;transform:translateY(8px)}to{opacity:1;transform:none}}@keyframes hmHeroSlide{from{opacity:.3;transform:translateX(55px)}to{opacity:1;transform:none}}@keyframes hmHeroZoom{from{opacity:.3;transform:scale(.94)}to{opacity:1;transform:scale(1)}}.hero-slide[data-transition="slide"].is-active{animation:hmHeroSlide .75s ease-out}.hero-slide[data-transition="zoom"].is-active{animation:hmHeroZoom .75s ease-out}.hero-featured-car{align-self:center;position:relative;width:min(440px,90%);justify-self:end;margin-right:4%;overflow:hidden;border:1px solid rgba(255,255,255,.7);border-radius:18px;background:white;color:var(--ink);box-shadow:0 22px 55px rgba(20,55,40,.18);text-decoration:none}.hero-featured-car>img,.hero-featured-placeholder{display:block;width:100%;height:290px;object-fit:cover;background:#dce8df}.hero-featured-placeholder{display:grid;place-items:center;color:#176b4d;font-size:72px;font-weight:900}.hero-offer-badge{position:absolute;z-index:2;top:14px;left:14px;padding:8px 11px;border-radius:999px;background:#e6b45a;color:#17231f;font-size:12px;font-weight:900;box-shadow:0 6px 18px rgba(0,0,0,.12)}.hero-featured-copy{display:grid;gap:3px;padding:15px 18px}.hero-featured-copy small{color:#176b4d;font-weight:900}.hero-featured-copy strong{font-size:20px}.hero-featured-copy span{font-size:18px;font-weight:900;color:#176b4d}.hero-featured-copy b{margin-top:7px;color:#0e4d37}@media(max-width:850px){.hero-slide{min-height:580px}.hero-grid{align-content:start}.hero-featured-car{width:min(360px,100%);justify-self:center;margin:10px auto 50px}.hero-featured-car>img,.hero-featured-placeholder{height:210px}.hero-carousel-controls{bottom:14px}}
    </style>
    @forelse ($heroSlides as $index => $slide)
        <article class="hero-slide {{ $index === 0 ? 'is-active' : '' }}" data-hero-slide="{{ $index }}" data-transition="{{ $slide->transition_style }}" data-autoplay="{{ $slide->autoplay_seconds }}" @if($slide->image_path) style="background-image:url('{{ \Illuminate\Support\Facades\Storage::disk('public')->url($slide->image_path) }}')" @endif>
            <div class="container hero-grid">
                <div class="hero-copy">
                    <span class="eyebrow"><span class="eyebrow-dot"></span> {{ $slide->eyebrow }}</span>
                    <h1>{{ $slide->title }}<br>@if($slide->highlight)<em>{{ $slide->highlight }}</em>@endif</h1>
                    <p>{{ $slide->description }}</p>
                    <div class="hero-actions">
                        @if($slide->primary_label && $slide->primary_url)
                            <a class="button button-primary" href="{{ url($slide->primary_url) }}">{{ $slide->primary_label }} <span aria-hidden="true">→</span></a>
                        @endif
                        @if($slide->secondary_label && $slide->secondary_url)
                            <a class="button button-ghost" href="{{ url($slide->secondary_url) }}">{{ $slide->secondary_label }}</a>
                        @endif
                        <a class="button button-ghost" href="{{ route('ownership-guide.show') }}">How to own a car</a>
                    </div>
                    <div class="trust-line"><span>✓</span> One team from inquiry to viewing</div>
                </div>
                @if($slide->featuredVehicle && $slide->featuredVehicle->status === 'published')
                    @php($featured = $slide->featuredVehicle)
                    <a class="hero-featured-car" href="{{ route('vehicles.show', $featured) }}" aria-label="View featured {{ $featured->year }} {{ $featured->make }} {{ $featured->model }}">
                        @if($slide->offer_badge)<span class="hero-offer-badge">{{ $slide->offer_badge }}</span>@endif
                        @if($featured->primaryImage)
                            <img
                                src="{{ route('vehicles.images.show', ['vehicle' => $featured->slug, 'image' => $featured->primaryImage->id]) }}"
                                alt="{{ $featured->year }} {{ $featured->make }} {{ $featured->model }}"
                            >
                        @else
                            <div class="hero-featured-copy"><small>FEATURED THIS WEEK</small><strong>{{ $featured->year }} {{ $featured->make }} {{ $featured->model }}</strong><span>KSh {{ number_format((float)$featured->price) }}</span><b>View this car →</b></div>
                        @endif
                    </a>
                @elseif(!$slide->image_path)
                    <div class="hero-art" aria-label="HM CARS vehicle marketplace illustration">
                        <div class="art-sun"></div><div class="art-road"></div>
                        <div class="art-card"><span class="art-card-label">YOUR NEXT DRIVE</span><div class="car-illustration"><div class="car-window"></div><div class="car-body"></div><div class="wheel wheel-one"></div><div class="wheel wheel-two"></div></div><div class="art-card-footer"><span>Find your match</span><span class="art-arrow">↗</span></div></div>
                        <div class="floating-note"><span class="note-icon">✓</span><span><strong>Guided by our team</strong><small>From inquiry to viewing</small></span></div>
                    </div>
                @endif
            </div>
        </article>
    @empty
        <article class="hero-slide is-active"><div class="container hero-grid"><div class="hero-copy"><span class="eyebrow"><span class="eyebrow-dot"></span> YOUR CAR. YOUR NEXT CHAPTER.</span><h1>Find the car that fits your life.<br><em>HM CARS helps you every step.</em></h1><p>Explore quality listings, get personal guidance, and move confidently from inquiry to viewing.</p><div class="hero-actions"><a class="button button-primary" href="{{ route('vehicles.index') }}">Explore cars →</a><a class="button button-ghost" href="#inquiry-form">Tell us what you need</a></div></div></div></article>
    @endforelse
    @if($heroSlides->count() > 1)
        <div class="hero-carousel-controls" role="tablist" aria-label="Choose homepage message">
            @foreach($heroSlides as $index => $slide)
                <button class="hero-dot {{ $index === 0 ? 'is-active' : '' }}" type="button" data-hero-dot="{{ $index }}" aria-label="Show {{ $slide->title }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}"></button>
            @endforeach
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const slides = [...document.querySelectorAll('[data-hero-slide]')];
                const dots = [...document.querySelectorAll('[data-hero-dot]')];
                if (!slides.length) return;
                let current = 0, timer;
                const show = (index) => {
                    current = (index + slides.length) % slides.length;
                    slides.forEach((slide, i) => slide.classList.toggle('is-active', i === current));
                    dots.forEach((dot, i) => { dot.classList.toggle('is-active', i === current); dot.setAttribute('aria-selected', i === current ? 'true' : 'false'); });
                };
                const restart = () => { clearInterval(timer); const seconds = Number(slides[current]?.dataset.autoplay || 6); timer = setInterval(() => { show(current + 1); restart(); }, Math.max(3, seconds) * 1000); };
                dots.forEach((dot, index) => dot.addEventListener('click', () => { show(index); restart(); }));
                slides[0]?.parentElement.addEventListener('mouseenter', () => clearInterval(timer));
                slides[0]?.parentElement.addEventListener('mouseleave', restart);
                restart();
            });
        </script>
    @endif
</section>
<section class="search-band">
    <div class="container">
        <form class="search-form" method="GET" action="{{ route('vehicles.index') }}">
            <label>Make or brand<input name="make" placeholder="e.g. Toyota"></label>
            <label>Location<input name="location" placeholder="e.g. Nairobi"></label>
            <label>Maximum budget<select name="max_price"><option value="">Any budget</option><option value="800000">Up to KSh 800,000</option><option value="1500000">Up to KSh 1.5M</option><option value="2500000">Up to KSh 2.5M</option><option value="4000000">Up to KSh 4M</option></select></label>
            <button class="button button-primary" type="submit">Search cars <span aria-hidden="true">→</span></button>
        </form>
    </div>
</section>
<section class="section container">
    <div class="section-heading"><div><span class="eyebrow">A GOOD PLACE TO START</span><h2>Explore available cars</h2><p>Listings are a starting point. Our team can help confirm availability and arrange the next step.</p></div><a class="text-link" href="{{ route('vehicles.index') }}">View all cars <span aria-hidden="true">→</span></a></div>
    <div class="vehicle-grid">
        @forelse ($vehicles as $vehicle)
            @include('partials.vehicle-card', ['vehicle' => $vehicle])
        @empty
            <div class="empty-state"><h3>Our first listings are on the way</h3><p>Check back soon or send HM CARS an inquiry and tell us what you're looking for.</p><a class="button button-primary" href="#inquiry-form">Tell us what you need</a></div>
        @endforelse
    </div>
</section>
<section class="process-section" id="how-it-works">
    <div class="container"><div class="section-heading centered"><div><span class="eyebrow">SIMPLE. PERSONAL. SUPPORTED.</span><h2>A better way to find your car</h2><p>You won't be left to figure it all out alone.</p></div></div>
        <div class="process-grid">
            <article class="process-card"><span class="process-number">01</span><div class="process-icon">⌕</div><h3>Find a car</h3><p>Browse listings and choose a vehicle that fits your needs and budget.</p></article>
            <article class="process-card"><span class="process-number">02</span><div class="process-icon">✆</div><h3>Talk to HM CARS</h3><p>Our team learns what you need and checks the next steps with you.</p></article>
            <article class="process-card"><span class="process-number">03</span><div class="process-icon">↗</div><h3>Arrange a viewing</h3><p>We coordinate with the seller and help move the conversation forward.</p></article>
        </div>
    </div>
</section>
<section class="inquiry-section" id="inquiry-form">
    <div class="container inquiry-grid">
        <div><span class="eyebrow">CAN'T FIND YOUR CAR?</span><h2>Tell us what you're looking for.</h2><p>Share your details and our team will contact you. Your request goes to HM CARS first—not directly to a seller.</p><div class="contact-promise"><span>✓</span> Your inquiry is handled by HM CARS</div><div class="contact-promise"><span>✓</span> Seller contact details stay private</div></div>
        <form class="inquiry-form" method="POST" action="{{ route('inquiries.store') }}">
            @csrf
            <h3>Request a callback</h3><p class="form-intro">We'll use these details to respond to your request.</p>
            <label>Your name<input name="customer_name" value="{{ old('customer_name') }}" required maxlength="120" autocomplete="name" placeholder="Full name"></label>
            <label>Phone number<input name="customer_phone" value="{{ old('customer_phone') }}" required maxlength="40" autocomplete="tel" placeholder="e.g. 07XX XXX XXX"></label>
            <label>Email (optional)<input type="email" name="customer_email" value="{{ old('customer_email') }}" maxlength="190" autocomplete="email" placeholder="you@example.com"></label>
            <label>What car are you looking for?<textarea name="message" required maxlength="2000" rows="3" placeholder="Tell us your preferred car, budget and location.">{{ old('message') }}</textarea></label>
            <label class="consent"><input type="checkbox" name="privacy_consent" value="1" required><span>I agree that HM CARS may use these details to respond to my inquiry. See our privacy notice before launch.</span></label>
            <button class="button button-primary button-full" type="submit">Send inquiry to HM CARS <span aria-hidden="true">→</span></button>
            <small class="form-note">This is a local development starter. Add a full privacy notice before collecting real customer data.</small>
        </form>
    </div>
</section>
@endsection
