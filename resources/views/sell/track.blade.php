@extends('layouts.app')

@section('title', 'Submission status')

@section('content')
<section class="page-hero">
    <div class="container">
        <span class="eyebrow">PRIVATE SUBMISSION TRACKING</span>
        <h1>Your vehicle submission</h1>
        <p>This private link is the way to check updates. Keep it somewhere safe and don't share it publicly.</p>
    </div>
</section>

<section class="container sell-section">
    <div class="submission-status-panel">
        <span class="eyebrow">CURRENT STATUS</span>
        <h2>
            @switch($submission->status)
                @case('pending_review') Pending Review @break
                @case('approved') Approved @break
                @case('rejected') Rejected @break
                @case('more_information_required') More Information Required @break
                @default Status update
            @endswitch
        </h2>
        <p class="submission-vehicle-title">{{ $submission->year }} {{ $submission->make }} {{ $submission->model }}</p>
        <p>Submitted {{ $submission->created_at->format('d M Y') }}</p>

        @if ($submission->admin_feedback)
            <div class="submission-feedback">
                <strong>Message from HM CARS</strong>
                <p>{{ $submission->admin_feedback }}</p>
            </div>
        @endif

        @if ($submission->seller_response)
            <div class="submission-feedback seller-response">
                <strong>Your additional information</strong>
                <p>{{ $submission->seller_response }}</p>
            </div>
        @endif

        @if ($submission->status === 'approved' && $submission->approvedVehicle)
            <a class="button button-primary" href="{{ route('vehicles.show', $submission->approvedVehicle) }}">View your marketplace listing</a>
        @elseif ($submission->status === 'pending_review')
            <p>Our team will review your details and update this page when there's news.</p>
        @elseif ($submission->status === 'rejected')
            <p>This vehicle was not published. Contact HM CARS if you have questions about the review.</p>
        @endif
    </div>

    @if ($submission->status === 'more_information_required')
        <style>
            .seller-update-form{max-width:960px}.seller-update-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.seller-update-grid label,.seller-update-full{display:grid;gap:7px}.seller-update-grid input,.seller-update-grid select,.seller-update-full textarea,.seller-update-full input{width:100%;min-height:44px;padding:10px 12px;border:1px solid #cbd4ce;border-radius:8px;font:inherit}.seller-update-full textarea{min-height:110px}.seller-update-full{gap:7px;margin-top:14px}.seller-update-photo-list{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}.seller-update-photo-list img{width:120px;height:90px;border-radius:8px;object-fit:cover}@media(max-width:680px){.seller-update-grid{grid-template-columns:1fr}}
        </style>
        <form class="inquiry-form sell-form seller-update-form" method="POST" action="{{ route('sell.information', ['token' => request()->route('token')]) }}" enctype="multipart/form-data">
            @csrf
            <h2>Correct or complete your submission</h2>
            <p class="form-intro">Update the original details below and answer HM CARS's request. You do not need to submit a new vehicle.</p>

            <div class="seller-update-grid">
                <label>Full name<input name="seller_name" required maxlength="120" value="{{ old('seller_name',$submission->seller_name) }}"></label>
                <label>Phone number<input name="seller_phone" required maxlength="40" value="{{ old('seller_phone',$submission->seller_phone) }}"></label>
                <label>Email address<input type="email" name="seller_email" required maxlength="190" value="{{ old('seller_email',$submission->seller_email) }}"></label>
                <label>Make<input name="make" required maxlength="80" value="{{ old('make',$submission->make) }}"></label>
                <label>Model<input name="model" required maxlength="100" value="{{ old('model',$submission->model) }}"></label>
                <label>Year<input type="number" name="year" required min="1900" max="2100" value="{{ old('year',$submission->year) }}"></label>
                <label>Asking price (KSh)<input type="number" name="price" required min="1" step="1" value="{{ old('price',$submission->price) }}"></label>
                <label>Vehicle location<input name="location" required maxlength="120" value="{{ old('location',$submission->location) }}"></label>
                <label>Transmission<select name="transmission" required>@foreach(['Automatic','Manual','CVT','Other'] as $value)<option value="{{ $value }}" @selected(old('transmission',$submission->transmission)===$value)> {{ $value }}</option>@endforeach</select></label>
                <label>Fuel type<select name="fuel_type" required>@foreach(['Petrol','Diesel','Hybrid','Electric','Other'] as $value)<option value="{{ $value }}" @selected(old('fuel_type',$submission->fuel_type)===$value)> {{ $value }}</option>@endforeach</select></label>
                <label>Mileage in kilometres<input type="number" name="mileage_km" required min="0" value="{{ old('mileage_km',$submission->mileage_km) }}"></label>
            </div>
            <label class="seller-update-full">Vehicle description<textarea name="description" required maxlength="10000">{{ old('description',$submission->description) }}</textarea></label>

            @if($submission->images->isNotEmpty())
                <div class="seller-update-full"><strong>{{ $submission->images->count() }} photo{{ $submission->images->count() === 1 ? '' : 's' }} already attached</strong><p class="form-intro">Adding photos here will keep the existing photos and attach your corrections.</p></div>
            @endif
            <label class="seller-update-full">Add corrected or extra photos (optional; JPG, PNG or WebP, up to 5 MB each)<input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple></label>
            @error('photos')<span class="field-error">{{ $message }}</span>@enderror

            <label class="seller-update-full">Answer the HM CARS request and explain every correction<textarea name="seller_response" rows="6" required minlength="10" maxlength="5000" placeholder="Example: I added the missing service history and corrected the mileage.">{{ old('seller_response') }}</textarea></label>
            @error('seller_response')<span class="field-error">{{ $message }}</span>@enderror
            <button class="button button-primary button-full" type="submit">Send corrections to HM CARS</button>
        </form>
    @endif
</section>
@endsection