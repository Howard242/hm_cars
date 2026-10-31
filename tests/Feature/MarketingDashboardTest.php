<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehiclePromotion;
use App\Models\VehiclePromotionVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_platform_totals_and_campaign_performance(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $vehicle = $this->vehicle();

        $facebook = VehiclePromotion::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $admin->id,
            'platform' => 'facebook',
        ]);
        $whatsapp = VehiclePromotion::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $admin->id,
            'platform' => 'whatsapp',
        ]);

        foreach ([$facebook, $facebook, $whatsapp] as $promotion) {
            VehiclePromotionVisit::create([
                'vehicle_promotion_id' => $promotion->id,
            ]);
        }

        foreach ([$facebook, $whatsapp] as $promotion) {
            Inquiry::create([
                'vehicle_id' => $vehicle->id,
                'customer_name' => 'Buyer',
                'customer_phone' => '0700000000',
                'message' => 'Interested in the vehicle.',
                'status' => 'new',
                'vehicle_promotion_id' => $promotion->id,
                'utm_source' => $promotion->platform,
                'utm_medium' => 'social',
                'utm_campaign' => 'vehicle-'.$vehicle->id.'-promotion-'.$promotion->id,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.marketing.index'))
            ->assertOk()
            ->assertViewHas('totals', fn ($totals) => $totals->promotions === 2
                && $totals->visits === 3
                && $totals->inquiries === 2
            )
            ->assertViewHas('eligibleVehicles', 1)
            ->assertViewHas('platformStats', fn ($stats) => $stats->count() === 4
                && $stats->firstWhere('platform', 'facebook')->visits_count === 2
                && $stats->firstWhere('platform', 'whatsapp')->inquiries_count === 1
                && $stats->firstWhere('platform', 'instagram')->promotions_count === 0
                && $stats->firstWhere('platform', 'tiktok')->promotions_count === 0
            )
            ->assertSee('Marketing performance')
            ->assertSee('Performance by platform')
            ->assertSee('Campaign history')
            ->assertSee('Toyota Corolla')
            ->assertSee('Facebook')
            ->assertDontSee('0712345678');
    }

    public function test_staff_without_admin_privileges_cannot_view_marketing_metrics(): void
    {
        $staff = User::factory()->create();
        $staff->forceFill(['is_staff' => true])->save();

        $this->actingAs($staff)
            ->get(route('admin.marketing.index'))
            ->assertForbidden();
    }

    private function vehicle(): Vehicle
    {
        return Vehicle::create([
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'price' => 1850000,
            'location' => 'Nairobi',
            'slug' => 'marketing-dashboard-toyota-corolla',
            'status' => 'published',
            'seller_phone' => '0712345678',
            'marketing_authorized_at' => now(),
        ]);
    }
}
