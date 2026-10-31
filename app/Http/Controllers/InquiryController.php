<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\VehiclePromotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InquiryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['nullable', 'integer', Rule::exists('vehicles', 'id')->where('status', 'published')],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'customer_email' => ['nullable', 'email', 'max:190'],
            'message' => ['required', 'string', 'max:2000'],
            'privacy_consent' => ['accepted'],
            'vehicle_promotion_id' => [
                'nullable',
                'integer',
                Rule::exists('vehicle_promotions', 'id')
                    ->where('vehicle_id', $request->input('vehicle_id')),
            ],
            'utm_source' => ['nullable', 'in:facebook,whatsapp,instagram,tiktok'],
            'utm_medium' => ['nullable', Rule::in(['social'])],
            'utm_campaign' => ['nullable', 'string', 'max:100'],
        ]);

        unset($validated['privacy_consent']);

        $promotion = null;
        if (! empty($validated['vehicle_promotion_id'])) {
            $promotion = VehiclePromotion::query()
                ->findOrFail($validated['vehicle_promotion_id']);
            $expectedCampaign = 'vehicle-'.$promotion->vehicle_id.'-promotion-'.$promotion->id;

            if (
                $promotion->vehicle_id !== (int) $validated['vehicle_id']
                || $promotion->platform !== ($validated['utm_source'] ?? null)
                || ($validated['utm_medium'] ?? null) !== 'social'
                || ($validated['utm_campaign'] ?? null) !== $expectedCampaign
            ) {
                throw ValidationException::withMessages([
                    'vehicle_promotion_id' => 'The promotion tracking details are invalid.',
                ]);
            }
        }

        $validated['vehicle_promotion_id'] = $promotion?->id;
        $validated['utm_source'] = $promotion?->platform;
        $validated['utm_medium'] = $promotion ? 'social' : null;
        $validated['utm_campaign'] = $promotion
            ? 'vehicle-'.$promotion->vehicle_id.'-promotion-'.$promotion->id
            : null;
        $validated['status'] = 'new';
        $validated['source'] = 'website';

        Inquiry::create($validated);

        return back()->with('success', 'Thank you. Your inquiry has been sent to HM CARS. Our team will contact you before arranging a connection with the seller.');
    }
}
