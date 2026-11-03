<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleSubmission;
use App\Models\VehicleSubmissionImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        $statuses = [
            VehicleSubmission::STATUS_PENDING_REVIEW,
            VehicleSubmission::STATUS_APPROVED,
            VehicleSubmission::STATUS_REJECTED,
            VehicleSubmission::STATUS_MORE_INFORMATION_REQUIRED,
        ];

        $validated = $request->validate([
            'status' => ['nullable', 'in:' . implode(',', $statuses)],
        ]);

        $query = VehicleSubmission::withCount('images')->latest();

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $submissions = $query->paginate(20)->withQueryString();

        return view('admin.submissions.index', compact('submissions', 'statuses'));
    }

    public function show(Request $request, VehicleSubmission $submission)
    {
        $this->ensureAdmin($request);
        $submission->load(['images', 'approvedVehicle']);

        return view('admin.submissions.show', compact('submission'));
    }

    public function photo(
    Request $request,
    VehicleSubmission $submission,
    VehicleSubmissionImage $image
) {
    $this->ensureAdmin($request);

    abort_unless(
        $image->vehicle_submission_id === $submission->id,
        404
    );

    $disk = Storage::disk('b2');
    $path = $image->image_path;

    abort_unless($disk->exists($path), 404);

    $stream = $disk->readStream($path);

    abort_if($stream === false, 404);

    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
    $contentType = $disk->mimeType($path) ?: '';

    abort_unless(
        in_array($contentType, ['image/jpeg', 'image/png', 'image/webp'], true),
        404
    );

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

    public function approve(Request $request, VehicleSubmission $submission)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'admin_feedback' => ['nullable', 'string', 'max:5000'],
        ]);

        $b2Paths = [];

        try {
            $vehicle = DB::transaction(function () use (
                $request,
                $submission,
                $validated,
                &$b2Paths
            ) {
                $lockedSubmission = VehicleSubmission::with('images')
                    ->lockForUpdate()
                    ->findOrFail($submission->id);

                if ($lockedSubmission->status === VehicleSubmission::STATUS_APPROVED) {
                    return $lockedSubmission->approvedVehicle;
                }

                $this->ensureReviewable($lockedSubmission);

                if ($lockedSubmission->images()->count() < 1) {
                    abort(422, 'A vehicle must have at least one photo before it can be approved.');
                }

                $vehicle = Vehicle::create([
                    'make' => $lockedSubmission->make,
                    'model' => $lockedSubmission->model,
                    'year' => $lockedSubmission->year,
                    'price' => $lockedSubmission->price,
                    'location' => $lockedSubmission->location,
                    'transmission' => $lockedSubmission->transmission,
                    'fuel_type' => $lockedSubmission->fuel_type,
                    'mileage_km' => $lockedSubmission->mileage_km,
                    'description' => $lockedSubmission->description,
                    'slug' => $this->uniqueSlug($lockedSubmission),
                    'status' => 'published',
                    'seller_name' => $lockedSubmission->seller_name,
                    'seller_phone' => $lockedSubmission->seller_phone,
                    'marketing_authorized_at' => $lockedSubmission->marketing_authorized_at,
                ]);

                foreach ($lockedSubmission->images as $index => $image) {
                    $extension = strtolower(pathinfo($image->image_path, PATHINFO_EXTENSION));
                    $extension = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)
                        ? $extension
                        : 'jpg';
                    $b2Path = 'vehicles/' . $vehicle->id . '/' . Str::uuid() . '.' . $extension;
                    $b2Paths[] = $b2Path;

                    $stream = Storage::disk('b2')->readStream($image->image_path);

                    if ($stream === false) {
                        throw new \RuntimeException('Unable to read a submitted vehicle photo.');
                    }

                    try {
                        $stored = Storage::disk('b2')->writeStream($b2Path, $stream);
                    } finally {
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }

                    if ($stored === false) {
                        throw new \RuntimeException('Unable to publish a submitted vehicle photo.');
                    }

                    $vehicle->images()->create([
                        'image_path' => $b2Path,
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                    ]);
                }

                $lockedSubmission->update([
                    'status' => VehicleSubmission::STATUS_APPROVED,
                    'approved_vehicle_id' => $vehicle->id,
                    'admin_feedback' => $validated['admin_feedback'] ?? null,
                    'reviewed_by_user_id' => $request->user()->id,
                    'reviewed_at' => now(),
                ]);

                return $vehicle;
            });
        } catch (\Throwable $exception) {
            try {
                Storage::disk('b2')->delete($b2Paths);
            } catch (\Throwable $cleanupException) {
                report($cleanupException);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('success', 'Submission approved and vehicle published.');
    }

    public function reject(Request $request, VehicleSubmission $submission)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'admin_feedback' => ['required', 'string', 'min:5', 'max:5000'],
        ]);

        $this->updateReviewStatus(
            $request,
            $submission,
            VehicleSubmission::STATUS_REJECTED,
            $validated['admin_feedback']
        );

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('success', 'Submission rejected and feedback sent to the seller.');
    }

    public function requestInformation(Request $request, VehicleSubmission $submission)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'admin_feedback' => ['required', 'string', 'min:5', 'max:5000'],
        ]);

        $this->updateReviewStatus(
            $request,
            $submission,
            VehicleSubmission::STATUS_MORE_INFORMATION_REQUIRED,
            $validated['admin_feedback']
        );

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('success', 'The seller has been asked for more information.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }

    private function ensureReviewable(VehicleSubmission $submission): void
    {
        abort_unless(
            in_array($submission->status, [
                VehicleSubmission::STATUS_PENDING_REVIEW,
                VehicleSubmission::STATUS_MORE_INFORMATION_REQUIRED,
            ], true),
            409
        );
    }

    private function updateReviewStatus(
        Request $request,
        VehicleSubmission $submission,
        string $status,
        string $feedback
    ): void {
        DB::transaction(function () use ($request, $submission, $status, $feedback) {
            $lockedSubmission = VehicleSubmission::query()
                ->lockForUpdate()
                ->findOrFail($submission->id);

            $this->ensureReviewable($lockedSubmission);
            $lockedSubmission->update([
                'status' => $status,
                'admin_feedback' => $feedback,
                'reviewed_by_user_id' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });
    }

    private function uniqueSlug(VehicleSubmission $submission): string
    {
        $baseSlug = Str::slug(
            $submission->make . ' ' . $submission->model . ' ' . $submission->year
        ) ?: 'vehicle';

        $slug = $baseSlug;
        $counter = 2;

        while (Vehicle::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
