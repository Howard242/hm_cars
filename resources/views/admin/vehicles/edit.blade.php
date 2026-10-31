<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HM CARS | Edit Vehicle</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f3f6fa; color: #1b2638; }
        header { background: #10233f; color: white; padding: 24px; }
        header h1 { margin: 0 0 8px; }
        header p { margin: 0; color: #d8e2f0; }
        .container { max-width: 950px; margin: 30px auto; padding: 0 16px; }
        .card { background: white; padding: 28px; border-radius: 10px; box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06); }
        .top-bar { display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 20px; }
        .top-bar h2 { margin: 0; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .field { display: flex; flex-direction: column; gap: 7px; min-width: 0; }
        .field.full { grid-column: 1 / -1; }
        label { font-weight: bold; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px; font: inherit; background: white; }
        textarea { min-height: 130px; resize: vertical; }
        input:focus, select:focus, textarea:focus { outline: 2px solid #2563eb; outline-offset: 1px; }
        .hint { font-size: 12px; color: #64748b; }
        .section-title { grid-column: 1 / -1; margin: 15px 0 0; padding-bottom: 10px; border-bottom: 1px solid #e5eaf1; }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 25px; }
        .button { display: inline-block; padding: 12px 18px; border-radius: 6px; border: none; background: #10233f; color: white; font-size: 14px; text-decoration: none; cursor: pointer; }
        .button:hover { background: #1c3b65; }
        .button.secondary { background: #e2e8f0; color: #1b2638; }
        .error-box { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .field-error { color: #b42318; font-size: 12px; }
        .privacy-note { background: #eff6ff; border-left: 4px solid #2563eb; padding: 12px; margin: 12px 0; grid-column: 1 / -1; font-size: 14px; }
        @media (max-width: 650px) { .grid { grid-template-columns: 1fr; } .card { padding: 18px; } }
    </style>
</head>
<body>
<header>
    <h1>HM CARS — Edit Vehicle</h1>
    <p>The Right Car. The Right Connection.</p>
</header>
<main class="container">
    <div class="top-bar">
        <h2>Edit Vehicle #{{ $vehicle->id }} — {{ $vehicle->make }} {{ $vehicle->model }}</h2>
        <a href="{{ route('admin.vehicles.index') }}" class="button secondary">Back to Vehicles</a>
    </div>
    <div class="card">
        @if ($errors->any())
            <div class="error-box">
                <strong>Please correct the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}">
            @csrf
            @method('PUT')
            <div class="grid">
                <h3 class="section-title">Vehicle Information</h3>

                <div class="field">
                    <label for="make">Make *</label>
                    <input type="text" id="make" name="make" maxlength="80" value="{{ old('make', $vehicle->make) }}" placeholder="e.g. Toyota" required>
                    @error('make') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="model">Model *</label>
                    <input type="text" id="model" name="model" maxlength="100" value="{{ old('model', $vehicle->model) }}" placeholder="e.g. Axio" required>
                    @error('model') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="year">Year *</label>
                    <input type="number" id="year" name="year" min="1900" max="2100" value="{{ old('year', $vehicle->year) }}" required>
                    @error('year') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="price">Price (KES) *</label>
                    <input type="number" id="price" name="price" min="0.01" step="0.01" value="{{ old('price', $vehicle->price) }}" placeholder="e.g. 1250000" required>
                    @error('price') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="location">Location *</label>
                    <input type="text" id="location" name="location" maxlength="120" value="{{ old('location', $vehicle->location) }}" placeholder="e.g. Nairobi" required>
                    @error('location') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="mileage_km">Mileage (km)</label>
                    <input type="number" id="mileage_km" name="mileage_km" min="0" value="{{ old('mileage_km', $vehicle->mileage_km) }}" placeholder="e.g. 85000">
                    @error('mileage_km') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="transmission">Transmission</label>
                    <select id="transmission" name="transmission">
                        <option value="">Select transmission</option>
                        @foreach (['Automatic', 'Manual', 'CVT'] as $transmission)
                            <option value="{{ $transmission }}" @selected(old('transmission', $vehicle->transmission) === $transmission)>{{ $transmission }}</option>
                        @endforeach
                    </select>
                    @error('transmission') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="fuel_type">Fuel Type</label>
                    <select id="fuel_type" name="fuel_type">
                        <option value="">Select fuel type</option>
                        @foreach (['Petrol', 'Diesel', 'Hybrid', 'Electric'] as $fuel)
                            <option value="{{ $fuel }}" @selected(old('fuel_type', $vehicle->fuel_type) === $fuel)>{{ $fuel }}</option>
                        @endforeach
                    </select>
                    @error('fuel_type') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field full">
                    <label for="description">Vehicle Description</label>
                    <textarea id="description" name="description" maxlength="10000" placeholder="Describe the vehicle condition, features and other important details.">{{ old('description', $vehicle->description) }}</textarea>
                    @error('description') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <h3 class="section-title">Private Seller Information</h3>
                <div class="privacy-note">Seller contact details are for internal HM CARS use only. They must not be displayed on public vehicle pages.</div>
                <div class="field">
                    <label for="seller_name">Seller Name</label>
                    <input type="text" id="seller_name" name="seller_name" maxlength="120" value="{{ old('seller_name', $vehicle->seller_name) }}" placeholder="Seller's full name">
                    @error('seller_name') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="seller_phone">Seller Phone</label>
                    <input type="tel" id="seller_phone" name="seller_phone" maxlength="40" value="{{ old('seller_phone', $vehicle->seller_phone) }}" placeholder="e.g. 07XXXXXXXX">
                    @error('seller_phone') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field full">
                    <label>
                        <input type="checkbox"
                               name="marketing_authorized"
                               value="1"
                               @checked(old('marketing_authorized', $vehicle->marketing_authorized_at !== null))>
                        I confirm the vehicle owner authorized HM CARS to use the vehicle photos and details in advertising.
                    </label>
                    <span class="hint">Record the owner's permission before selecting this option. Unchecking it disables promotion.</span>
                    @error('marketing_authorized') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="actions">
                <button type="submit" class="button">Update Vehicle</button>
                <a href="{{ route('admin.vehicles.index') }}" class="button secondary">Cancel</a>
            </div>
            <p class="hint">* Required fields. Updating vehicle details does not change its publication status or URL slug.</p>
        </form>
    </div>
</main>
</body>
</html>
