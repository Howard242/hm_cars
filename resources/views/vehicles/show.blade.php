
@extends('layouts.app')

@section('title', $vehicle->year . ' ' . $vehicle->make . ' ' . $vehicle->model . ' | KSh ' . number_format((float) $vehicle->price))
@section('meta_description', 'Explore this ' . $vehicle->year . ' ' . $vehicle->make . ' ' . $vehicle->model . ' for KSh ' . number_format((float) $vehicle->price) . '. Contact HM CARS to confirm availability and arrange a viewing.')

@section('content')

<section class="container detail-section">

    <a class="back-link" href="{{ route('vehicles.index') }}">
        ← Back to cars
    </a>

    <div class="detail-grid">

        {{-- LEFT: VEHICLE GALLERY AND DESCRIPTION --}}
        <div>

            <div class="detail-image">

                @if($vehicle->images->isNotEmpty())

                    @php
                        $mainImage = $vehicle->primaryImage
                            ?? $vehicle->images->first();
                    @endphp

                    {{-- Main vehicle image --}}
                    <div class="vehicle-gallery-main">
                        <img
                            id="mainVehicleImage"
                            src="{{ route('vehicles.images.show', ['vehicle' => $vehicle->slug, 'image' => $mainImage->id]) }}"
                            alt="{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}"
                        >
                    </div>

                    {{-- Clickable photo thumbnails --}}
                    <div class="vehicle-gallery-thumbnails">

                        @foreach($vehicle->images as $image)

                            <button
                                type="button"
                                class="vehicle-gallery-thumbnail {{ $image->id === $mainImage->id ? 'active' : '' }}"
                                data-image="{{ route('vehicles.images.show', ['vehicle' => $vehicle->slug, 'image' => $image->id]) }}"
                                aria-label="View vehicle photo {{ $loop->iteration }}"
                                aria-pressed="{{ $image->id === $mainImage->id ? 'true' : 'false' }}"
                            >
                                <img
                                    src="{{ route('vehicles.images.show', ['vehicle' => $vehicle->slug, 'image' => $image->id]) }}"
                                    alt="{{ $vehicle->make }} {{ $vehicle->model }} photo {{ $loop->iteration }}"
                                    loading="lazy"
                                >
                            </button>

                        @endforeach

                    </div>

                    <p class="vehicle-gallery-count">
                        {{ $vehicle->images->count() }} vehicle photo(s)
                    </p>

                @else

                    {{-- Placeholder for vehicles without photos --}}
                    <div class="vehicle-placeholder large">

                        <span>
                            {{ strtoupper(
                                substr($vehicle->make, 0, 1) .
                                substr($vehicle->model, 0, 1)
                            ) }}
                        </span>

                        <small>
                            VEHICLE PHOTOS WILL APPEAR HERE
                        </small>

                    </div>

                @endif

            </div>

            {{-- VEHICLE DESCRIPTION --}}
            <div class="detail-description">

                <h2>About this car</h2>

                <p>
                    {{ $vehicle->description ?: 'Contact HM CARS for more details about this vehicle.' }}
                </p>

                <p>
                    Our team can help confirm availability
                    and coordinate a viewing with the seller.
                </p>

            </div>

        </div>

        {{-- RIGHT: VEHICLE DETAILS --}}
        <aside class="detail-panel">

            <span class="eyebrow">
                HM CARS LISTING
            </span>

            <h1>
                {{ $vehicle->year }}
                {{ $vehicle->make }}
                {{ $vehicle->model }}
            </h1>

            <p class="detail-price">
                KSh {{ number_format((float) $vehicle->price) }}
            </p>

            <div class="detail-specs">

                <div>
                    <small>Location</small>
                    <strong>{{ $vehicle->location }}</strong>
                </div>

                <div>
                    <small>Transmission</small>
                    <strong>
                        {{ $vehicle->transmission ?: 'Ask our team' }}
                    </strong>
                </div>

                <div>
                    <small>Fuel type</small>
                    <strong>
                        {{ $vehicle->fuel_type ?: 'Ask our team' }}
                    </strong>
                </div>

                <div>
                    <small>Mileage</small>
                    <strong>
                            {{ $vehicle->mileage_km !== null
                                ? number_format($vehicle->mileage_km) . ' km'
                            : 'Ask our team' }}
                    </strong>
                </div>

            </div>

            <div class="detail-trust-list" aria-label="How HM CARS helps">
                <div class="detail-trust-item">
                    <span class="detail-trust-number">01</span>
                    <div>
                        <strong>Confirm availability</strong>
                        <p>Our team checks with the seller before you make plans.</p>
                    </div>
                </div>
                <div class="detail-trust-item">
                    <span class="detail-trust-number">02</span>
                    <div>
                        <strong>Arrange a viewing</strong>
                        <p>We help coordinate a suitable time with the seller.</p>
                    </div>
                </div>
                <div class="detail-trust-item">
                    <span class="detail-trust-number">03</span>
                    <div>
                        <strong>Your details stay with HM CARS</strong>
                        <p>Seller contact details are shared through our team.</p>
                    </div>
                </div>
            </div>

            <a
                class="button button-primary button-full"
                href="#vehicle-inquiry"
            >
                Send an inquiry
            </a>

            @if(config('hmcars.phone'))
                <a
                    class="button button-outline button-full"
                    href="tel:+{{ preg_replace('/\D+/', '', config('hmcars.phone')) }}"
                >
                    Call HM CARS
                </a>

            @else

                <p class="contact-pending">
                    Our official phone number is being set up.
                    Send an online inquiry below and our team
                    can follow up once contact details are configured.
                </p>

            @endif

            @if(config('hmcars.whatsapp'))
                <a
                    class="button button-whatsapp button-full"
                    target="_blank"
                    rel="noopener noreferrer"
                    href="https://wa.me/{{ preg_replace('/\D+/', '', config('hmcars.whatsapp')) }}?text={{ rawurlencode('Hello HM CARS, I am interested in ' . $vehicle->year . ' ' . $vehicle->make . ' ' . $vehicle->model . ' (listing HMC-' . str_pad((string) $vehicle->id, 4, '0', STR_PAD_LEFT) . '). Please help me with the next steps.') }}"
                >
                    WhatsApp HM CARS
                </a>
                <p class="whatsapp-note">
                    WhatsApp opens with your message ready. Review it and press Send to contact us.
                </p>

            @endif

            <small class="listing-reference">
                Listing reference:
                HMC-{{ str_pad((string) $vehicle->id, 4, '0', STR_PAD_LEFT) }}
            </small>

        </aside>

    </div>

    {{-- BUYER INQUIRY SECTION --}}
    <div class="vehicle-inquiry-section" id="vehicle-inquiry">

        <div>

            <span class="eyebrow">
                INTERESTED IN THIS CAR?
            </span>

            <h2>
                Let HM CARS help you take the next step.
            </h2>

            <p>
                Your details go to our team.
                Seller contact information is not shown on this page.
            </p>

        </div>

        <form
            class="inquiry-form"
            method="POST"
            action="{{ route('inquiries.store') }}"
        >

            @csrf

            <input
                type="hidden"
                name="vehicle_id"
                value="{{ $vehicle->id }}"
            >

            @if ($campaignTracking)
                <input type="hidden" name="vehicle_promotion_id" value="{{ $campaignTracking['promotion_id'] }}">
                <input type="hidden" name="utm_source" value="{{ $campaignTracking['utm_source'] }}">
                <input type="hidden" name="utm_medium" value="{{ $campaignTracking['utm_medium'] }}">
                <input type="hidden" name="utm_campaign" value="{{ $campaignTracking['utm_campaign'] }}">
            @endif

            <label>
                Your name

                <input
                    name="customer_name"
                    value="{{ old('customer_name') }}"
                    required
                    maxlength="120"
                    autocomplete="name"
                >
            </label>

            <label>
                Phone number

                <input
                    name="customer_phone"
                    value="{{ old('customer_phone') }}"
                    required
                    maxlength="40"
                    autocomplete="tel"
                    placeholder="e.g. 07XX XXX XXX"
                >
            </label>

            <label>
                Email (optional)

                <input
                    type="email"
                    name="customer_email"
                    value="{{ old('customer_email') }}"
                    maxlength="190"
                    autocomplete="email"
                >
            </label>

            <label>
                Message

                <textarea
                    name="message"
                    required
                    maxlength="2000"
                    rows="3"
                >{{ old('message', "I'm interested in the {$vehicle->year} {$vehicle->make} {$vehicle->model}. Please contact me with the next steps.") }}</textarea>

            </label>

            <label class="consent">

                <input
                    type="checkbox"
                    name="privacy_consent"
                    value="1"
                    required
                    @checked(old('privacy_consent'))
                >

                <span>
                    I agree that HM CARS may use these details
                    to respond to my inquiry.
                </span>

            </label>

            <button
                class="button button-primary button-full"
                type="submit"
            >
                Send inquiry to HM CARS
            </button>

        </form>

    </div>

</section>

{{-- PHOTO GALLERY INTERACTION --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const mainImage = document.getElementById('mainVehicleImage');

    const thumbnails = document.querySelectorAll(
        '.vehicle-gallery-thumbnail'
    );

    if (!mainImage) {
        return;
    }

    thumbnails.forEach(function (thumbnail) {

        thumbnail.addEventListener('click', function () {

            const selectedImage = this.dataset.image;

            if (!selectedImage) {
                return;
            }

            mainImage.src = selectedImage;

            thumbnails.forEach(function (item) {
                item.classList.remove('active');
                item.setAttribute('aria-pressed', 'false');
            });

            this.classList.add('active');
            this.setAttribute('aria-pressed', 'true');

        });

    });

});
</script>

@endsection
