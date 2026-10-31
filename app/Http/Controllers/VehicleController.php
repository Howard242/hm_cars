<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehiclePromotion;
use App\Models\VehiclePromotionVisit;
use App\Models\HomeHeroSlide;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /**
     * Display the public homepage.
     */
    public function home(): View
    {
        // Get the latest 6 published vehicles,
        // including each vehicle's main photo.
        $vehicles = Vehicle::published()
            ->with('primaryImage')
            ->latest()
            ->take(6)
            ->get();

        $heroSlides = HomeHeroSlide::published()
            ->with(['featuredVehicle.primaryImage'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('home', compact('vehicles', 'heroSlides'));
    }

    /**
     * Display published vehicles with search filters.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'make' => ['nullable', 'string', 'max:80'],
            'model' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:120'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'year_from' => ['nullable', 'integer', 'between:1900,2100'],
            'year_to' => ['nullable', 'integer', 'between:1900,2100'],
            'transmission' => ['nullable', 'string', 'max:40'],
            'fuel_type' => ['nullable', 'string', 'max:40'],
            'sort' => ['nullable', 'in:newest,price_low,price_high,year_new,year_old'],
        ]);

        if (
            isset($filters['min_price'], $filters['max_price']) &&
            (float) $filters['min_price'] > (float) $filters['max_price']
        ) {
            throw ValidationException::withMessages([
                'max_price' => 'Maximum price must be greater than or equal to minimum price.',
            ]);
        }

        if (
            isset($filters['year_from'], $filters['year_to']) &&
            (int) $filters['year_from'] > (int) $filters['year_to']
        ) {
            throw ValidationException::withMessages([
                'year_to' => 'Latest year must be greater than or equal to earliest year.',
            ]);
        }

        // Only show published vehicles and load their main photos.
        $query = Vehicle::published()
            ->with('primaryImage');

        foreach (['make', 'model', 'location'] as $field) {
            if (!empty($filters[$field])) {
                $query->where($field, 'like', '%' . $filters[$field] . '%');
            }
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['year_from'])) {
            $query->where('year', '>=', $filters['year_from']);
        }

        if (isset($filters['year_to'])) {
            $query->where('year', '<=', $filters['year_to']);
        }

        foreach (['transmission', 'fuel_type'] as $field) {
            if (!empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        $query = match ($filters['sort'] ?? 'newest') {
            'price_low' => $query->orderBy('price')->orderByDesc('id'),
            'price_high' => $query->orderByDesc('price')->orderByDesc('id'),
            'year_new' => $query->orderByDesc('year')->orderByDesc('id'),
            'year_old' => $query->orderBy('year')->orderByDesc('id'),
            default => $query->latest(),
        };

        $vehicles = $query
            ->paginate(9)
            ->withQueryString();

        $transmissions = Vehicle::published()
            ->whereNotNull('transmission')
            ->where('transmission', '!=', '')
            ->distinct()
            ->orderBy('transmission')
            ->pluck('transmission');

        $fuelTypes = Vehicle::published()
            ->whereNotNull('fuel_type')
            ->where('fuel_type', '!=', '')
            ->distinct()
            ->orderBy('fuel_type')
            ->pluck('fuel_type');

        return view('vehicles.index', compact(
            'vehicles',
            'transmissions',
            'fuelTypes'
        ));
    }

    /**
     * Display the admin vehicle creation page.
     *
     * Preserved from your existing controller.
     */
    public function create(Request $request)
    {
        abort_unless($request->user()?->is_admin, 403);

        return view('admin.vehicles.create');
    }

    /**
     * Store a new vehicle.
     *
     * Preserved from your existing controller.
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
                'max:9999999999.99'
            ],
            'location' => ['required', 'string', 'max:120'],
            'transmission' => ['nullable', 'string', 'max:40'],
            'fuel_type' => ['nullable', 'string', 'max:40'],
            'mileage_km' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:10000'],
            'seller_name' => ['nullable', 'string', 'max:120'],
            'seller_phone' => ['nullable', 'string', 'max:40'],
        ]);

        // Newly added vehicles are not publicly visible
        // until an administrator publishes them.
        $validated['status'] = 'pending';

        // Generate a unique slug for the public vehicle URL.
        $baseSlug = \Illuminate\Support\Str::slug(
            $validated['make'] . ' ' .
            $validated['model'] . ' ' .
            $validated['year']
        );

        $baseSlug = $baseSlug ?: 'vehicle';

        $slug = $baseSlug;
        $counter = 2;

        while (\App\Models\Vehicle::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;

        \App\Models\Vehicle::create($validated);

        return redirect()
            ->route('admin.vehicles.index')
            ->with(
                'success',
                'Vehicle added successfully as pending.'
            );
    }

    /**
     * Display a published vehicle's details.
     */
    public function show(Request $request, Vehicle $vehicle): View
    {
        // Prevent buyers from accessing unpublished vehicles.
        abort_unless($vehicle->status === 'published', 404);

        $campaignTracking = null;
        $platform = $request->query('utm_source');
        $campaign = $request->query('utm_campaign');
        $promotionMatch = is_string($campaign)
            ? preg_match('/^vehicle-(\d+)-promotion-(\d+)$/', $campaign, $matches)
            : false;

        if (
            $request->query('utm_medium') === 'social'
            && in_array($platform, ['facebook', 'whatsapp', 'instagram', 'tiktok'], true)
            && $promotionMatch === 1
            && (int) $matches[1] === $vehicle->id
        ) {
            $promotion = VehiclePromotion::query()
                ->whereKey((int) $matches[2])
                ->where('vehicle_id', $vehicle->id)
                ->where('platform', $platform)
                ->first();

            if ($promotion) {
                $campaignTracking = [
                    'promotion_id' => $promotion->id,
                    'utm_source' => $platform,
                    'utm_medium' => 'social',
                    'utm_campaign' => $campaign,
                ];

                $visitSessionKey = 'vehicle_promotion_visit_'.$promotion->id;

                if (! $request->session()->get($visitSessionKey)) {
                    VehiclePromotionVisit::create([
                        'vehicle_promotion_id' => $promotion->id,
                    ]);
                    $request->session()->put($visitSessionKey, true);
                }
            }
        }

        // Load all vehicle photos and the main photo.
        $vehicle->load([
            'images',
            'primaryImage',
        ]);

        // Keep seller contact details private.
        return view('vehicles.show', compact('vehicle', 'campaignTracking'));
    }
}
