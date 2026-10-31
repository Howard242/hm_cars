<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehiclePromotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehiclePromotionController extends Controller
{
    public function show(Request $request, Vehicle $vehicle): View
    {
        $this->ensureAdmin($request);
        $this->ensurePromotable($vehicle);

        $vehicle->load('images');

        $promotions = $vehicle->promotions()
            ->with('promoter')
            ->withCount(['visits', 'inquiries'])
            ->latest()
            ->paginate(15);

        $publicUrl = route('vehicles.show', ['vehicle' => $vehicle->slug]);
        $caption = $this->defaultCaption($vehicle, $publicUrl);
        $contactPhone = config('hmcars.phone');

        return view('admin.vehicles.promote', compact(
            'vehicle',
            'promotions',
            'publicUrl',
            'caption',
            'contactPhone'
        ));
    }

    public function store(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->ensureAdmin($request);
        $this->ensurePromotable($vehicle);

        $validated = $request->validate([
            'platform' => ['required', 'in:facebook,whatsapp,instagram,tiktok'],
            'caption' => ['nullable', 'string', 'max:2000'],
        ]);

        $promotion = $vehicle->promotions()->create([
            'user_id' => $request->user()->id,
            'platform' => $validated['platform'],
        ]);

        $publicUrl = route('vehicles.show', ['vehicle' => $vehicle->slug]);
        $trackedUrl = $this->trackedUrl($publicUrl, $promotion);
        $caption = trim((string) ($validated['caption'] ?? ''));

        if ($caption === '') {
            $caption = $this->defaultCaption($vehicle, $trackedUrl);
        } else {
            $caption = str_replace($publicUrl, $trackedUrl, $caption);

            if (! str_contains($caption, $trackedUrl)) {
                $caption .= "\n\n".$trackedUrl;
            }
        }

        if ($validated['platform'] === 'facebook') {
            $shareUrl = 'https://www.facebook.com/sharer/sharer.php?'.http_build_query(
                ['u' => $trackedUrl],
                '',
                '&',
                PHP_QUERY_RFC3986
            );

            return redirect()->away($shareUrl);
        }

        if ($validated['platform'] === 'whatsapp') {
            return redirect()->away('https://wa.me/?text='.rawurlencode($caption));
        }

        return redirect()
            ->route('admin.vehicles.promote', $vehicle)
            ->with('prepared_platform', $validated['platform'])
            ->with('prepared_caption', $caption)
            ->with('success', 'Promotion prepared. Review the caption and branded photos before publishing.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }

    private function ensurePromotable(Vehicle $vehicle): void
    {
        abort_unless(
            $vehicle->status === 'published' && $vehicle->marketing_authorized_at !== null,
            404
        );
    }

    private function trackedUrl(string $publicUrl, VehiclePromotion $promotion): string
    {
        $campaign = 'vehicle-'.$promotion->vehicle_id.'-promotion-'.$promotion->id;

        return $publicUrl.'?'.http_build_query([
            'utm_source' => $promotion->platform,
            'utm_medium' => 'social',
            'utm_campaign' => $campaign,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    private function defaultCaption(Vehicle $vehicle, string $url): string
    {
        return implode("\n", [
            $vehicle->year.' '.$vehicle->make.' '.$vehicle->model,
            'KSh '.number_format((float) $vehicle->price),
            $vehicle->location,
            $url,
        ]);
    }
}
