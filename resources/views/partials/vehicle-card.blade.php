
<article class="vehicle-card">

    {{-- Vehicle main image --}}
    @if($vehicle->primaryImage)

        <div class="vehicle-image">
            <img
                src="{{ route('vehicles.images.show', ['vehicle' => $vehicle->slug, 'image' => $vehicle->primaryImage->id]) }}"
                alt="{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}"
                loading="lazy"
            >
        </div>

    @else

        {{-- Placeholder for vehicles without photos --}}
        <div class="vehicle-placeholder">
            <span>
                {{ strtoupper(
                    substr($vehicle->make, 0, 1) .
                    substr($vehicle->model, 0, 1)
                ) }}
            </span>

            <small>HM CARS LISTING</small>
        </div>

    @endif

    <div class="vehicle-card-body">

        <div class="vehicle-meta">
            <span>{{ $vehicle->year }}</span>
            <span>{{ $vehicle->location }}</span>
        </div>

        <h3>
            <a href="{{ route('vehicles.show', $vehicle->slug) }}">
                {{ $vehicle->make }} {{ $vehicle->model }}
            </a>
        </h3>

        <p class="price">
            KSh {{ number_format((float) $vehicle->price) }}
        </p>

        <div class="spec-row">
            <span>
                {{ $vehicle->transmission ?: 'Transmission on request' }}
            </span>

            <span>
                {{ $vehicle->fuel_type ?: 'Fuel on request' }}
            </span>
        </div>

        <a class="button button-outline button-full"
           href="{{ route('vehicles.show', $vehicle->slug) }}">
            View car details
        </a>

    </div>

</article>
