<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleImage;
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

        $storedPaths = [];

        try {
            DB::transaction(function () use (
                $vehicle,
                $validated,
                &$storedPaths
            ) {
                $lockedVehicle = Vehicle::whereKey($vehicle->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $currentCount = $lockedVehicle->images()->count();
                $newCount = count($validated['photos']);

                if ($currentCount + $newCount > 10) {
                    throw ValidationException::withMessages([
                        'photos' => 'A vehicle can have a maximum of 10 photos.',
                    ]);
                }

                if ($currentCount === 0 && $newCount < 5) {
                    throw ValidationException::withMessages([
                        'photos' => 'Please upload at least 5 photos for a new vehicle gallery.',
                    ]);
                }

                foreach ($validated['photos'] as $index => $photo) {
                    $path = $photo->store(
                        'vehicles/' . $lockedVehicle->id,
                        'b2'
                    );

                    if ($path === false) {
                        throw new \RuntimeException(
                            'Unable to save an uploaded photo.'
                        );
                    }

                    $storedPaths[] = $path;

                    $lockedVehicle->images()->create([
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
                try {
                    Storage::disk('b2')->delete($path);
                } catch (\Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.vehicles.images.index', $vehicle)
            ->with('success', 'Vehicle photos uploaded successfully.');
    }

    public function photo(
        Request $request,
        Vehicle $vehicle,
        VehicleImage $image
    ) {
        abort_unless($request->user()?->is_admin, 403);

        abort_unless($image->vehicle_id === $vehicle->id, 404);

        $disk = Storage::disk('b2');
        $path = $image->image_path;

        abort_unless($disk->exists($path), 404);

        $stream = $disk->readStream($path);

        abort_if($stream === false, 404);

        try {
            $contentType = $disk->mimeType($path) ?: '';
        } catch (\Throwable $exception) {
            if (is_resource($stream)) {
                fclose($stream);
            }

            throw $exception;
        }

        if (! in_array($contentType, [
            'image/jpeg',
            'image/png',
            'image/webp',
        ], true)) {
            if (is_resource($stream)) {
                fclose($stream);
            }

            abort(404);
        }

        return response()->stream(
            function () use ($stream) {
                try {
                    fpassthru($stream);
                } finally {
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }
            },
            200,
            [
                'Content-Type' => $contentType,
                'Cache-Control' => 'private, no-store',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }
    public function setPrimary(
        Request $request,
        Vehicle $vehicle,
        VehicleImage $image
    ) {
        abort_unless($request->user()?->is_admin, 403);

        abort_unless($image->vehicle_id === $vehicle->id, 404);

        DB::transaction(function () use ($vehicle, $image) {
            $lockedVehicle = Vehicle::whereKey($vehicle->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedImage = $lockedVehicle->images()
                ->whereKey($image->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedVehicle->images()->update([
                'is_primary' => false,
            ]);

            $lockedImage->update([
                'is_primary' => true,
            ]);
        }, 3);

        return redirect()
            ->route('admin.vehicles.images.index', $vehicle)
            ->with('success', 'Main vehicle photo updated successfully.');
    }
    public function replace(
        Request $request,
        Vehicle $vehicle,
        VehicleImage $image
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
            'b2'
        );

        if ($newPath === false) {
            throw new \RuntimeException(
                'Unable to save the replacement photo.'
            );
        }

        $oldPath = null;

        try {
            DB::transaction(function () use (
                $vehicle,
                $image,
                $newPath,
                &$oldPath
            ) {
                $lockedVehicle = Vehicle::whereKey($vehicle->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $lockedImage = $lockedVehicle->images()
                    ->whereKey($image->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $oldPath = $lockedImage->image_path;

                $lockedImage->update([
                    'image_path' => $newPath,
                ]);
            });
        } catch (\Throwable $exception) {
            try {
                Storage::disk('b2')->delete($newPath);
            } catch (\Throwable $cleanupException) {
                report($cleanupException);
            }

            throw $exception;
        }

        if ($oldPath !== null) {
            try {
                Storage::disk('b2')->delete($oldPath);
            } catch (\Throwable $cleanupException) {
                report($cleanupException);
            }
        }

        return redirect()
            ->route('admin.vehicles.images.index', $vehicle)
            ->with('success', 'Vehicle photo replaced successfully.');
    }

    public function destroy(
        Request $request,
        Vehicle $vehicle,
        VehicleImage $image
    ) {
        abort_unless($request->user()?->is_admin, 403);

        abort_unless($image->vehicle_id === $vehicle->id, 404);

        $imagePath = null;
        $deletionBlocked = false;

        DB::transaction(function () use (
            $vehicle,
            $image,
            &$imagePath,
            &$deletionBlocked
        ) {
            $lockedVehicle = Vehicle::whereKey($vehicle->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedImage = $lockedVehicle->images()
                ->whereKey($image->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedVehicle->images()->count() <= 5) {
                $deletionBlocked = true;
                return;
            }

            $imagePath = $lockedImage->image_path;
            $wasPrimary = $lockedImage->is_primary;

            $lockedImage->delete();

            if ($wasPrimary) {
                $nextImage = $lockedVehicle->images()->first();

                if ($nextImage) {
                    $nextImage->update([
                        'is_primary' => true,
                    ]);
                }
            }
        }, 3);

        if ($deletionBlocked) {
            return redirect()
                ->route('admin.vehicles.images.index', $vehicle)
                ->with(
                    'error',
                    'A vehicle must retain at least 5 photos. Upload another photo before deleting this one.'
                );
        }

        try {
            Storage::disk('b2')->delete($imagePath);
        } catch (\Throwable $cleanupException) {
            report($cleanupException);
        }

        return redirect()
            ->route('admin.vehicles.images.index', $vehicle)
            ->with('success', 'Vehicle photo deleted successfully.');
    }

}
