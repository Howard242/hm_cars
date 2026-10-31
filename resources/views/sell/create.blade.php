@extends('layouts.app')

@section('title', 'Sell your car with HM CARS')

@section('content')
<style>
    .sell-page { padding-block: 36px 64px; }
    .sell-intro, .sell-form-card { padding: clamp(20px, 4vw, 36px); border: 1px solid #e2e7e3; border-radius: 18px; background: #fff; box-shadow: 0 18px 45px rgba(25, 45, 35, .06); }
    .sell-intro { margin-bottom: 20px; background: #e6f1eb; }
    .sell-intro h1 { margin: 4px 0 10px; font-size: clamp(2rem, 5vw, 3rem); line-height: 1.1; }
    .sell-intro p { max-width: 760px; margin: 0; color: #52615b; }
    .sell-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 22px; }
    .sell-step { display: flex; gap: 11px; align-items: flex-start; }
    .sell-step-number { display: grid; flex: 0 0 30px; width: 30px; height: 30px; place-items: center; border-radius: 50%; background: #176b4d; color: white; font-weight: 800; }
    .sell-step strong, .sell-step small { display: block; }
    .sell-step small { margin-top: 3px; color: #52615b; }
    .sell-form-card h2 { margin: 0 0 6px; font-size: 1.35rem; }
    .sell-form-card > p { margin: 0 0 20px; color: #65716d; }
    .sell-form { display: grid; gap: 22px; }
    .form-section { padding-top: 20px; border-top: 1px solid #e2e7e3; }
    .form-section:first-of-type { padding-top: 0; border-top: 0; }
    .form-section h3 { margin: 0 0 4px; font-size: 1.08rem; }
    .section-help { margin: 0 0 14px; color: #65716d; font-size: .9rem; }
    .field-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .sell-form label:not(.consent) { display: grid; gap: 6px; color: #24342d; font-weight: 700; }
    .sell-form input:not([type="checkbox"]):not([type="file"]), .sell-form select, .sell-form textarea { width: 100%; min-height: 46px; padding: 10px 12px; border: 1px solid #cbd4ce; border-radius: 8px; background: white; color: #17231f; font: inherit; }
    .sell-form textarea { min-height: 120px; resize: vertical; }
    .sell-form input:focus, .sell-form select:focus, .sell-form textarea:focus { outline: 3px solid rgba(23, 107, 77, .2); border-color: #176b4d; }
    .required-note { color: #65716d; font-size: .82rem; font-weight: 400; }
    .photo-picker { padding: 16px; border: 1px dashed #95aa9d; border-radius: 10px; background: #f7faf8; }
    .photo-picker input[type="file"] { display: block; width: 100%; margin-top: 10px; font: inherit; }
    .photo-feedback { margin: 8px 0 0; color: #52615b; font-size: .88rem; }
    .photo-feedback.invalid { color: #a32121; font-weight: 700; }
    .photo-preview { display: grid; grid-template-columns: repeat(auto-fill, minmax(105px, 1fr)); gap: 10px; margin-top: 14px; }
    .photo-preview figure { margin: 0; }
    .photo-preview img { display: block; width: 100%; aspect-ratio: 4 / 3; border-radius: 7px; object-fit: cover; background: #e9efeb; }
    .photo-preview figcaption { margin-top: 4px; overflow: hidden; color: #65716d; font-size: .73rem; text-overflow: ellipsis; white-space: nowrap; }
    .agreement { padding: 16px; border: 1px solid #e2e7e3; border-radius: 10px; background: #fbfcfb; }
    .agreement > p { margin: 0 0 10px; color: #52615b; }
    .agreement details { padding-top: 8px; }
    .agreement summary { color: #176b4d; cursor: pointer; font-weight: 750; }
    .agreement ol { padding-left: 22px; }
    .agreement li { margin-top: 10px; }
    .agreement-note { color: #65716d; font-size: .8rem; }
    .consent-list { display: grid; gap: 12px; }
    .sell-form .consent { display: flex; align-items: flex-start; gap: 10px; color: #394740; font-size: .9rem; line-height: 1.5; }
    .sell-form .consent input { flex: 0 0 auto; width: 18px; height: 18px; margin: 2px 0 0; accent-color: #176b4d; }
    .field-error { color: #a32121; font-size: .82rem; font-weight: 600; }
    .submit-row { display: grid; gap: 9px; }
    .submit-row .button { min-height: 50px; font-size: 1rem; }
    .privacy-note { margin: 0; color: #65716d; font-size: .82rem; text-align: center; }
    @media (max-width: 680px) { .sell-steps { grid-template-columns: 1fr; gap: 14px; } }
    @media (max-width: 520px) { .sell-page { padding-block: 20px 40px; } .field-grid { grid-template-columns: 1fr; } }
</style>

<section class="container sell-page">
    <div class="sell-intro">
        <span class="eyebrow">SELL WITH HM CARS</span>
        <h1>Let’s find the right buyer for your car.</h1>
        <p>Tell us about your vehicle and our team will review your submission before it appears on the marketplace. Your contact details and photos stay private during review.</p>
        <div class="sell-steps" aria-label="Three steps to submit your car">
            <div class="sell-step"><span class="sell-step-number">1</span><span><strong>Share your details</strong><small>Tell us how to reach you and describe the car.</small></span></div>
            <div class="sell-step"><span class="sell-step-number">2</span><span><strong>Add clear photos</strong><small>Choose 5–10 JPG, PNG or WebP photos.</small></span></div>
            <div class="sell-step"><span class="sell-step-number">3</span><span><strong>Track your submission</strong><small>Save the private link we give you after submitting.</small></span></div>
        </div>
    </div>

    <div class="sell-form-card">
        <h2>Submit your vehicle</h2>
        <p>Complete every field, choose 5–10 vehicle photos, and review the agreement before submitting.</p>

        <form class="sell-form" method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="request_key" value="{{ $requestKey }}">

            <section class="form-section" aria-labelledby="seller-details-heading">
                <h3 id="seller-details-heading">Your contact details</h3>
                <p class="section-help">We use these details to review your submission and contact you privately.</p>
                <div class="field-grid">
                    <label for="seller-name">Full name <span class="required-note">Required</span>
                        <input id="seller-name" name="seller_name" value="{{ old('seller_name') }}" required maxlength="120" autocomplete="name" aria-invalid="{{ $errors->has('seller_name') ? 'true' : 'false' }}">
                        @error('seller_name') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                    <label for="seller-phone">Phone number <span class="required-note">Required</span>
                        <input id="seller-phone" name="seller_phone" type="tel" value="{{ old('seller_phone') }}" required maxlength="40" autocomplete="tel" inputmode="tel" placeholder="e.g. 07XX XXX XXX" aria-invalid="{{ $errors->has('seller_phone') ? 'true' : 'false' }}">
                        @error('seller_phone') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                    <label for="seller-email">Email address <span class="required-note">Required</span>
                        <input id="seller-email" type="email" name="seller_email" value="{{ old('seller_email') }}" required maxlength="190" autocomplete="email" placeholder="you@example.com" aria-invalid="{{ $errors->has('seller_email') ? 'true' : 'false' }}">
                        @error('seller_email') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                </div>
            </section>

            <section class="form-section" aria-labelledby="vehicle-details-heading">
                <h3 id="vehicle-details-heading">Vehicle details</h3>
                <p class="section-help">Add the key details buyers usually ask about.</p>
                <div class="field-grid">
                    <label for="make">Make <span class="required-note">Required</span>
                        <input id="make" name="make" value="{{ old('make') }}" required maxlength="80" placeholder="e.g. Toyota" autocomplete="off" aria-invalid="{{ $errors->has('make') ? 'true' : 'false' }}">
                        @error('make') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                    <label for="model">Model <span class="required-note">Required</span>
                        <input id="model" name="model" value="{{ old('model') }}" required maxlength="100" placeholder="e.g. Fielder" autocomplete="off" aria-invalid="{{ $errors->has('model') ? 'true' : 'false' }}">
                        @error('model') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                    <label for="year">Year <span class="required-note">Required</span>
                        <input id="year" type="number" name="year" value="{{ old('year') }}" required min="1900" max="2100" step="1" inputmode="numeric" placeholder="e.g. 2020" aria-invalid="{{ $errors->has('year') ? 'true' : 'false' }}">
                        @error('year') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                    <label for="price">Asking price (KSh) <span class="required-note">Required</span>
                        <input id="price" type="number" name="price" value="{{ old('price') }}" required min="1" step="1" inputmode="decimal" placeholder="e.g. 1,850,000" aria-invalid="{{ $errors->has('price') ? 'true' : 'false' }}">
                        @error('price') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                    <label for="location">Vehicle location <span class="required-note">Required</span>
                        <input id="location" name="location" value="{{ old('location') }}" required maxlength="120" placeholder="Town or county" autocomplete="address-level2" aria-invalid="{{ $errors->has('location') ? 'true' : 'false' }}">
                        @error('location') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                    <label for="transmission">Transmission <span class="required-note">Required</span>
                        <select id="transmission" name="transmission" required aria-invalid="{{ $errors->has('transmission') ? 'true' : 'false' }}">
                            <option value="">Select transmission</option>
                            @foreach (['Automatic', 'Manual', 'CVT', 'Other'] as $transmission)
                                <option value="{{ $transmission }}" @selected(old('transmission') === $transmission)>{{ $transmission }}</option>
                            @endforeach
                        </select>
                        @error('transmission') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                    <label for="fuel-type">Fuel type <span class="required-note">Required</span>
                        <select id="fuel-type" name="fuel_type" required aria-invalid="{{ $errors->has('fuel_type') ? 'true' : 'false' }}">
                            <option value="">Select fuel type</option>
                            @foreach (['Petrol', 'Diesel', 'Hybrid', 'Electric', 'Other'] as $fuelType)
                                <option value="{{ $fuelType }}" @selected(old('fuel_type') === $fuelType)>{{ $fuelType }}</option>
                            @endforeach
                        </select>
                        @error('fuel_type') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                    <label for="mileage">Mileage in kilometres <span class="required-note">Required</span>
                        <input id="mileage" type="number" name="mileage_km" value="{{ old('mileage_km') }}" required min="0" step="1" inputmode="numeric" placeholder="e.g. 85,000" aria-invalid="{{ $errors->has('mileage_km') ? 'true' : 'false' }}">
                        @error('mileage_km') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                </div>
                <label for="description" style="margin-top:14px;">Description <span class="required-note">Required</span>
                    <textarea id="description" name="description" rows="4" required maxlength="10000" placeholder="Condition, service history, known repairs, features, or anything else buyers should know.">{{ old('description') }}</textarea>
                    @error('description') <span class="field-error">{{ $message }}</span> @enderror
                </label>
            </section>

            <section class="form-section" aria-labelledby="photos-heading">
                <h3 id="photos-heading">Vehicle photos</h3>
                <p class="section-help">Select 5–10 clear photos, up to 5 MB each. Good lighting and different angles help buyers assess the car.</p>
                <div class="photo-picker">
                    <label for="photos">Choose photos <span class="required-note">Required · 5–10 photos</span></label>
                    <input id="photos" type="file" name="photos[]" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple required aria-describedby="photo-feedback">
                    @if ($errors->any())
                        <span class="required-note">Photos are cleared by the browser if the form needs correction; please select them again.</span>
                    @endif
                    <p class="photo-feedback" id="photo-feedback" role="status" aria-live="polite">No photos selected yet.</p>
                    <div class="photo-preview" id="photo-preview"></div>
                </div>
                @error('photos') <p class="field-error" role="alert">{{ $message }}</p> @enderror
            </section>

            <section class="form-section" aria-labelledby="agreement-heading">
                <h3 id="agreement-heading">Review the seller agreement</h3>
                <div class="agreement">
                    <strong>{{ $terms->title }}</strong>
                    <p>{{ $terms->introduction }}</p>
                    <details>
                        <summary>Read the full agreement (version {{ $terms->version }})</summary>
                        <ol>
                            @foreach ($terms->clauses as $clause)
                                <li><strong>{{ $clause['title'] }}:</strong> {{ $clause['body'] }}</li>
                            @endforeach
                        </ol>
                    </details>
                    <p class="agreement-note">This brokerage agreement does not replace a separate sale agreement between you and the buyer.</p>
                </div>
                <input type="hidden" name="terms_version" value="{{ $terms->version }}">
                <div class="consent-list">
                    <label class="consent">
                        <input type="checkbox" name="terms_consent" value="1" required @checked(old('terms_consent'))>
                        <span>I have read and agree to the Seller Brokerage Agreement above, including the commission and responsibilities described.</span>
                    </label>
                    @error('terms_consent') <span class="field-error">{{ $message }}</span> @enderror
                    <label class="consent">
                        <input type="checkbox" name="privacy_consent" value="1" required @checked(old('privacy_consent'))>
                        <span>I agree HM CARS may use my contact details to review this submission and contact me about it. Seller contact details are not shown on public listings.</span>
                    </label>
                    @error('privacy_consent') <span class="field-error">{{ $message }}</span> @enderror
                    <label class="consent">
                        <input type="checkbox" name="marketing_consent" value="1" required @checked(old('marketing_consent'))>
                        <span>This permission is required to submit my vehicle. I authorize HM CARS to use my vehicle photos and listing details in marketing and social media posts.</span>
                    </label>
                    @error('marketing_consent') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </section>

            <div class="submit-row">
                <button class="button button-primary button-full" type="submit">Submit vehicle for review</button>
                <p class="privacy-note">After you submit, save your private tracking link to check your status or respond to questions.</p>
            </div>
        </form>
    </div>
</section>

<script>
    const photoInput = document.getElementById('photos');
    const photoFeedback = document.getElementById('photo-feedback');
    const photoPreview = document.getElementById('photo-preview');
    const sellForm = photoInput.closest('form');
    let previewUrls = [];

    function clearPreviews() {
        previewUrls.forEach((url) => URL.revokeObjectURL(url));
        previewUrls = [];
        photoPreview.replaceChildren();
    }

    photoInput.addEventListener('change', () => {
        clearPreviews();
        const files = Array.from(photoInput.files || []);
        const messages = [];

        if (files.length < 5 || files.length > 10) {
            messages.push('Choose between 5 and 10 photos.');
        }

        files.forEach((file, index) => {
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                messages.push('Photo ' + (index + 1) + ' must be JPG, PNG, or WebP.');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                messages.push('Photo ' + (index + 1) + ' is larger than 5 MB.');
                return;
            }

            const url = URL.createObjectURL(file);
            previewUrls.push(url);
            const figure = document.createElement('figure');
            const image = document.createElement('img');
            const caption = document.createElement('figcaption');
            image.src = url;
            image.alt = 'Selected vehicle photo ' + (index + 1);
            caption.textContent = file.name;
            figure.append(image, caption);
            photoPreview.append(figure);
        });

        photoFeedback.textContent = messages.length
            ? messages.join(' ')
            : files.length + ' photo' + (files.length === 1 ? '' : 's') + ' selected.';
        photoFeedback.classList.toggle('invalid', messages.length > 0);
        photoInput.setCustomValidity(messages.join(' '));
    });

    sellForm.addEventListener('submit', (event) => {
        const files = Array.from(photoInput.files || []);

        if (files.length < 5 || files.length > 10) {
            photoInput.setCustomValidity('Choose between 5 and 10 vehicle photos.');
            photoFeedback.textContent = 'Choose between 5 and 10 photos before submitting.';
            photoFeedback.classList.add('invalid');
        }

        if (!sellForm.reportValidity()) {
            event.preventDefault();
        }
    });
</script>
@endsection
