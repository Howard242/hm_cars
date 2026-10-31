<?php

namespace App\Http\Controllers;

use App\Models\SellerBrokerageTerms;
use App\Models\VehicleSubmission;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VehicleSubmissionController extends Controller
{
    public function create()
    {
        return view('sell.create', [
            'requestKey' => Str::random(64),
            'terms' => SellerBrokerageTerms::current(),
        ]);
    }

    public function store(Request $request)
    {
        $terms = SellerBrokerageTerms::current();
        $validated = $request->validate([
            'request_key' => ['required', 'string', 'size:64', 'alpha_num'],
            'seller_name' => ['required', 'string', 'max:120'],
            'seller_phone' => ['required', 'string', 'max:40'],
            'seller_email' => ['required', 'email', 'max:190'],
            'privacy_consent' => ['accepted'],
            'terms_consent' => ['accepted'],
            'marketing_consent' => ['accepted'],
            'terms_version' => ['required', 'string', Rule::in([$terms->version])],
            'make' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'between:1900,2100'],
            'price' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'location' => ['required', 'string', 'max:120'],
            'transmission' => ['required', 'string', 'max:40'],
            'fuel_type' => ['required', 'string', 'max:40'],
            'mileage_km' => ['required', 'integer', 'min:0'],
            'description' => ['required', 'string', 'max:10000'],
            'photos' => ['required', 'array', 'min:5', 'max:10'],
            'photos.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $requestKeyHash = hash('sha256', $validated['request_key']);

        if (VehicleSubmission::where('request_key_hash', $requestKeyHash)->exists()) {
            abort(409, 'This form has already been submitted. Use your existing tracking link.');
        }

        $trackingToken = bin2hex(random_bytes(32));
        $storedPaths = [];

        try {
            $submission = DB::transaction(function () use (
                $validated,
                $requestKeyHash,
                $trackingToken,
                &$storedPaths
            ) {
                $submission = VehicleSubmission::create([
                    'tracking_token_hash' => hash('sha256', $trackingToken),
                    'request_key_hash' => $requestKeyHash,
                    'seller_name' => $validated['seller_name'],
                    'seller_phone' => $validated['seller_phone'],
                    'seller_email' => $validated['seller_email'],
                    'make' => $validated['make'],
                    'model' => $validated['model'],
                    'year' => $validated['year'],
                    'price' => $validated['price'],
                    'location' => $validated['location'],
                    'transmission' => $validated['transmission'],
                    'fuel_type' => $validated['fuel_type'],
                    'mileage_km' => $validated['mileage_km'],
                    'description' => $validated['description'],
                    'terms_accepted_at' => now(),
                    'terms_version' => $validated['terms_version'],
                    'marketing_authorized_at' => now(),
                ]);

                foreach ($validated['photos'] as $index => $photo) {
                    $path = $photo->store(
                        'vehicle-submissions/'.$submission->id,
                        'local'
                    );

                    if ($path === false) {
                        throw new \RuntimeException('Unable to save an uploaded photo.');
                    }

                    $storedPaths[] = $path;
                    $submission->images()->create([
                        'image_path' => $path,
                        'sort_order' => $index,
                    ]);
                }

                return $submission;
            });
        } catch (QueryException $exception) {
            Storage::disk('local')->delete($storedPaths);

            if (VehicleSubmission::where('request_key_hash', $requestKeyHash)->exists()) {
                abort(409, 'This form has already been submitted. Use your existing tracking link.');
            }

            throw $exception;
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($storedPaths);
            throw $exception;
        }

        return redirect()->route('sell.track', ['token' => $trackingToken]);
    }

    public function track(string $token)
    {
        $submission = VehicleSubmission::with('approvedVehicle')
            ->where('tracking_token_hash', hash('sha256', $token))
            ->firstOrFail();

        return view('sell.track', compact('submission'));
    }

    public function provideInformation(Request $request, string $token)
    {
        $validated = $request->validate([
            'seller_response' => ['required', 'string', 'min:10', 'max:5000'],
            'seller_name' => ['required', 'string', 'max:120'],
            'seller_phone' => ['required', 'string', 'max:40'],
            'seller_email' => ['required', 'email', 'max:190'],
            'make' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'between:1900,2100'],
            'price' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'location' => ['required', 'string', 'max:120'],
            'transmission' => ['required', 'string', 'max:40'],
            'fuel_type' => ['required', 'string', 'max:40'],
            'mileage_km' => ['required', 'integer', 'min:0'],
            'description' => ['required', 'string', 'max:10000'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        DB::transaction(function () use ($token, $validated, $request) {
            $submission = VehicleSubmission::with('images')
                ->where('tracking_token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $submission->status === VehicleSubmission::STATUS_MORE_INFORMATION_REQUIRED,
                409
            );

            $newPhotos = $request->file('photos', []);
            if (count($submission->images) + count($newPhotos) > 10) {
                throw ValidationException::withMessages([
                    'photos' => 'This submission can contain no more than 10 photos in total.',
                ]);
            }

            $submission->update([
                'seller_name' => $validated['seller_name'],
                'seller_phone' => $validated['seller_phone'],
                'seller_email' => $validated['seller_email'],
                'make' => $validated['make'],
                'model' => $validated['model'],
                'year' => $validated['year'],
                'price' => $validated['price'],
                'location' => $validated['location'],
                'transmission' => $validated['transmission'],
                'fuel_type' => $validated['fuel_type'],
                'mileage_km' => $validated['mileage_km'],
                'description' => $validated['description'],
                'seller_response' => $validated['seller_response'],
                'status' => VehicleSubmission::STATUS_PENDING_REVIEW,
            ]);

            $nextSortOrder = (int) $submission->images()->max('sort_order') + 1;
            foreach ($newPhotos as $photo) {
                $path = $photo->store('vehicle-submissions/'.$submission->id, 'local');
                if ($path === false) {
                    throw new \RuntimeException('Unable to save an uploaded photo.');
                }
                $submission->images()->create([
                    'image_path' => $path,
                    'sort_order' => $nextSortOrder++,
                ]);
            }
        });

        return redirect()
            ->route('sell.track', ['token' => $token])
            ->with('success', 'Your corrections and additional information were sent to HM CARS.');
    }
}
