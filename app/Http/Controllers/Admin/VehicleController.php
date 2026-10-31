<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VehicleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DISPLAY ALL VEHICLES
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        abort_unless($request->user()?->is_admin, 403);

        $vehicles = Vehicle::query()
            ->latest()
            ->paginate(15);

        return view(
            'admin.vehicles.index',
            compact('vehicles')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW ADD VEHICLE FORM
    |--------------------------------------------------------------------------
    */
    public function create(Request $request)
    {
        abort_unless($request->user()?->is_admin, 403);

        return view('admin.vehicles.create');
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE NEW VEHICLE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        abort_unless($request->user()?->is_admin, 403);

        $validated = $request->validate([
            'make' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'between:1900,2100'],
            'price' => [
                'required',
                'numeric',
                'gt:0',
                'max:9999999999.99',
            ],
            'location' => ['required', 'string', 'max:120'],
            'transmission' => ['nullable', 'string', 'max:40'],
            'fuel_type' => ['nullable', 'string', 'max:40'],
            'mileage_km' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:10000'],
            'seller_name' => ['nullable', 'string', 'max:120'],
            'seller_phone' => ['nullable', 'string', 'max:40'],
            'marketing_authorized' => ['sometimes', 'accepted'],
        ]);

        $validated['marketing_authorized_at'] = isset($validated['marketing_authorized'])
            ? now()
            : null;
        unset($validated['marketing_authorized']);

        // New vehicles remain private until published.
        $validated['status'] = 'pending';

        // Generate a unique vehicle slug.
        $baseSlug = Str::slug(
            $validated['make'] . ' ' .
            $validated['model'] . ' ' .
            $validated['year']
        );

        $baseSlug = $baseSlug ?: 'vehicle';

        $slug = $baseSlug;
        $counter = 2;

        while (Vehicle::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;

        Vehicle::create($validated);

        return redirect()
            ->route('admin.vehicles.index')
            ->with(
                'success',
                'Vehicle added successfully as pending.'
            );
            
    }
    
/*
|--------------------------------------------------------------------------
| SHOW EDIT VEHICLE FORM
|--------------------------------------------------------------------------
*/
public function edit(Request $request, Vehicle $vehicle)
{
    abort_unless($request->user()?->is_admin, 403);

    return view('admin.vehicles.edit', compact('vehicle'));
}

public function update(Request $request, Vehicle $vehicle)
{
    abort_unless($request->user()?->is_admin, 403);

    $vehicle = Vehicle::findOrFail($vehicle->id);

    $validated = $request->validate([
        'make' => ['required', 'string', 'max:80'],
        'model' => ['required', 'string', 'max:100'],
        'year' => ['required', 'integer', 'between:1900,2100'],
        'price' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
        'location' => ['required', 'string', 'max:120'],
        'transmission' => ['nullable', 'string', 'max:40'],
        'fuel_type' => ['nullable', 'string', 'max:40'],
        'mileage_km' => ['nullable', 'integer', 'min:0'],
        'description' => ['nullable', 'string', 'max:10000'],
        'seller_name' => ['nullable', 'string', 'max:120'],
        'seller_phone' => ['nullable', 'string', 'max:40'],
        'marketing_authorized' => ['sometimes', 'accepted'],
    ]);

    $validated['marketing_authorized_at'] = isset($validated['marketing_authorized'])
        ? ($vehicle->marketing_authorized_at ?? now())
        : null;
    unset($validated['marketing_authorized']);

    $vehicle->update($validated);

    return redirect()
        ->route('admin.vehicles.index')
        ->with('success', 'Vehicle updated successfully.');
}

/*
|--------------------------------------------------------------------------
| PUBLISH OR UNPUBLISH VEHICLE
|--------------------------------------------------------------------------
*/
public function updatePublication(Request $request, Vehicle $vehicle)
{
    abort_unless($request->user()?->is_admin, 403);

    $validated = $request->validate([
        'status' => ['required', 'in:published,pending,sold,withdrawn'],
    ]);

    $vehicle->update([
        'status' => $validated['status'],
    ]);

    $message = match ($validated['status']) {
        'published' => 'Vehicle published successfully.',
        'sold' => 'Vehicle marked as sold and removed from the marketplace.',
        'withdrawn' => 'Vehicle withdrawn and removed from the marketplace.',
        default => 'Vehicle unpublished successfully.',
    };

    return redirect()
        ->route('admin.vehicles.index')
        ->with('success', $message);
}

}