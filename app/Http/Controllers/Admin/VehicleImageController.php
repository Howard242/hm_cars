<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class VehicleImageController extends Controller
{
    public function index(Request $request, Vehicle $vehicle)
    {
        abort_unless($request->user()?->is_admin, 403);

        $vehicle->load('images');

        return view('admin.vehicles.images', compact('vehicle'));
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        abort_unless($request->user()?->is_admin, 403);

        $validated = $request->validate([
            'photos' => ['required', 'array', 'min:1', 'max:10'],
            'photos.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $currentCount = $vehicle->images()->count();
        $newCount = count($validated['photos']);

        if ($currentCount + $newCount > 10) {
            throw ValidationException::withMessages([
                'photos' => 'A vehicle can have a maximum of 10 photos.',
            ]);
        }

        // The initial upload must contain at least five photos.
        if ($currentCount === 0 && $newCount < 5) {
            throw ValidationException::withMessages([
                'photos' => 'Please upload at least 5 photos for a new vehicle gallery.',
            ]);
        }

        $storedPaths = [];

        try {
            DB::transaction(function () use (
                $vehicle,
                $validated,
                $currentCount,
                &$storedPaths
            ) {
                foreach ($validated['photos'] as $index => $photo) {
                    $path = $photo->store(
                        'vehicles/' . $vehicle->id,
                        'public'
                    );

                    if ($path === false) {
                        throw new \RuntimeException(
                            'Unable to save an uploaded photo.'
                        );
                    }

                    $storedPaths[] = $path;

                    $vehicle->images()->create([
                        'image_path' => $path,
                        'is_primary' => (
                            $currentCount === 0 && $index === 0
                        ),
                        'sort_order' => $currentCount + $index,
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.vehicles.images.index', $vehicle)
            ->with('success', 'Vehicle photos uploaded successfully.');
    }
    
public function setPrimary(
    Request $request,
    Vehicle $vehicle,
    \App\Models\VehicleImage $image
) {
    abort_unless($request->user()?->is_admin, 403);

    // Ensure this photo belongs to the selected vehicle.
    abort_unless($image->vehicle_id === $vehicle->id, 404);

    // Only one image can be the main photo.
    DB::transaction(function () use ($vehicle, $image) {

        $vehicle->images()->update([
            'is_primary' => false,
        ]);

        $image->update([
            'is_primary' => true,
        ]);

    });

    return redirect()
        ->route('admin.vehicles.images.index', $vehicle)
        ->with('success', 'Main vehicle photo updated successfully.');
}

public function replace(
    Request $request,
    Vehicle $vehicle,
    \App\Models\VehicleImage $image
) {
    abort_unless($request->user()?->is_admin, 403);
    abort_unless($image->vehicle_id === $vehicle->id, 404);

    $validated = $request->validate([
        'photo' => [
            'required',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:5120',
        ],
    ]);

    $newPath = $validated['photo']->store(
        'vehicles/' . $vehicle->id,
        'public'
    );

    if ($newPath === false) {
        throw new \RuntimeException('Unable to save the replacement photo.');
    }

    $oldPath = $image->image_path;

    try {
        DB::transaction(function () use ($image, $newPath) {
            $image->update(['image_path' => $newPath]);
        });
    } catch (\Throwable $exception) {
        Storage::disk('public')->delete($newPath);
        throw $exception;
    }

    Storage::disk('public')->delete($oldPath);

    return redirect()
        ->route('admin.vehicles.images.index', $vehicle)
        ->with('success', 'Vehicle photo replaced successfully.');
}

public function destroy(
    Request $request,
    Vehicle $vehicle,
    \App\Models\VehicleImage $image
) {
    abort_unless($request->user()?->is_admin, 403);

    // Confirm that the image belongs to this vehicle.
    abort_unless($image->vehicle_id === $vehicle->id, 404);

    // Keep at least five photos per vehicle.
    if ($vehicle->images()->count() <= 5) {
        return redirect()
            ->route('admin.vehicles.images.index', $vehicle)
            ->with(
                'error',
                'A vehicle must retain at least 5 photos. Upload another photo before deleting this one.'
            );
    }

    $imagePath = $image->image_path;

    DB::transaction(function () use ($vehicle, $image) {

        $wasPrimary = $image->is_primary;

        $image->delete();

        // If the main photo was deleted, select another.
        if ($wasPrimary) {
            $nextImage = $vehicle->images()->first();

            if ($nextImage) {
                $nextImage->update([
                    'is_primary' => true,
                ]);
            }
        }
    });

    // Delete the physical image after the database update.
    Storage::disk('public')->delete($imagePath);

    return redirect()
        ->route('admin.vehicles.images.index', $vehicle)
        ->with('success', 'Vehicle photo deleted successfully.');
}

}
