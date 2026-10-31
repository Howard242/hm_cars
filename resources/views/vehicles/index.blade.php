
@extends('layouts.app')

@section('title', 'Browse cars')

@section('content')

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">THE HM CARS MARKETPLACE</span>

        <h1>Find a car that fits your life.</h1>

        <p>
            Browse listings, then contact HM CARS so our
            team can guide the next step.
        </p>
    </div>
</section>

<section class="container listing-section">

    <form class="search-form listing-search advanced-search"
          method="GET"
          action="{{ route('vehicles.index') }}">

        <label>
            Make
            <input
                name="make"
                value="{{ request('make') }}"
                placeholder="e.g. Toyota"
                maxlength="80"
            >
        </label>

        <label>
            Model
            <input
                name="model"
                value="{{ request('model') }}"
                placeholder="e.g. Fielder"
                maxlength="100"
            >
        </label>

        <label>
            Location
            <input
                name="location"
                value="{{ request('location') }}"
                placeholder="e.g. Nairobi"
                maxlength="120"
            >
        </label>

        <label>
            Minimum price (KSh)
            <input
                type="number"
                name="min_price"
                value="{{ request('min_price') }}"
                min="0"
                step="10000"
                placeholder="No minimum"
            >
        </label>

        <label>
            Maximum price (KSh)
            <input
                type="number"
                name="max_price"
                value="{{ request('max_price') }}"
                min="0"
                step="10000"
                placeholder="No maximum"
            >
        </label>

        <label>
            Earliest year
            <input
                type="number"
                name="year_from"
                value="{{ request('year_from') }}"
                min="1900"
                max="2100"
                placeholder="Any year"
            >
        </label>

        <label>
            Latest year
            <input
                type="number"
                name="year_to"
                value="{{ request('year_to') }}"
                min="1900"
                max="2100"
                placeholder="Any year"
            >
        </label>

        <label>
            Transmission
            <select name="transmission">
                <option value="">Any transmission</option>
                @foreach ($transmissions as $transmission)
                    <option value="{{ $transmission }}" @selected(request('transmission') === $transmission)>
                        {{ $transmission }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            Fuel type
            <select name="fuel_type">
                <option value="">Any fuel type</option>
                @foreach ($fuelTypes as $fuelType)
                    <option value="{{ $fuelType }}" @selected(request('fuel_type') === $fuelType)>
                        {{ $fuelType }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            Sort by
            <select name="sort">
                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest listings</option>
                <option value="price_low" @selected(request('sort') === 'price_low')>Price: low to high</option>
                <option value="price_high" @selected(request('sort') === 'price_high')>Price: high to low</option>
                <option value="year_new" @selected(request('sort') === 'year_new')>Year: newest first</option>
                <option value="year_old" @selected(request('sort') === 'year_old')>Year: oldest first</option>
            </select>
        </label>

        <button class="button button-primary" type="submit">
            Search vehicles
        </button>

        <a class="search-reset" href="{{ route('vehicles.index') }}">Clear filters</a>

    </form>

    <div class="results-bar">
        <p>
            <strong>{{ $vehicles->total() }}</strong>
            car listing(s)
        </p>

        <span>
            Seller contact details are kept private
        </span>
    </div>

    <div class="vehicle-grid">

        @forelse ($vehicles as $vehicle)

            @include('partials.vehicle-card', [
                'vehicle' => $vehicle
            ])

        @empty

            <div class="empty-state">
                <h3>No cars matched those filters</h3>

                <p>
                    Try another make, location or budget.
                </p>

                <a class="button button-outline"
                   href="{{ route('vehicles.index') }}">
                    Clear filters
                </a>
            </div>

        @endforelse

    </div>

    <div class="pagination-wrap">
        {{ $vehicles->links() }}
    </div>

</section>

@endsection
