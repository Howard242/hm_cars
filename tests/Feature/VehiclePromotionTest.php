<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehiclePromotionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_prepare_promotions_for_an_authorized_published_vehicle(): void
    {
        $admin = $this->admin();
        $vehicle = $this->vehicle();

        $this->actingAs($admin)
            ->get(route('admin.vehicles.promote', $vehicle))
            ->assertOk()
            ->assertSee('Promote vehicle')
            ->assertSee('Branded photos for Instagram and TikTok')
            ->assertSee('Prepare TikTok photo post')
            ->assertDontSee('0712345678');
    }

    public function test_non_admin_cannot_open_or_create_a_promotion(): void
    {
        $user = User::factory()->create();
        $vehicle = $this->vehicle();

        $this->actingAs($user)
            ->get(route('admin.vehicles.promote', $vehicle))
            ->assertForbidden();

        $this->post(route('admin.vehicles.promotions.store', $vehicle), [
            'platform' => 'facebook',
        ])->assertForbidden();
    }

    public function test_only_published_vehicles_with_marketing_authorization_can_be_promoted(): void
    {
        $admin = $this->admin();
        $unauthorized = $this->vehicle(['marketing_authorized_at' => null]);
        $sold = $this->vehicle(['status' => 'sold']);
        $withdrawn = $this->vehicle(['status' => 'withdrawn']);

        foreach ([$unauthorized, $sold, $withdrawn] as $vehicle) {
            $this->actingAs($admin)
                ->get(route('admin.vehicles.promote', $vehicle))
                ->assertNotFound();

            $this->post(route('admin.vehicles.promotions.store', $vehicle), [
                'platform' => 'facebook',
            ])->assertNotFound();
        }

        $this->assertDatabaseCount('vehicle_promotions', 0);
    }

    public function test_facebook_share_uses_the_public_campaign_link_without_seller_contact_details(): void
    {
        $admin = $this->admin();
        $vehicle = $this->vehicle();

        $response = $this->actingAs($admin)
            ->post(route('admin.vehicles.promotions.store', $vehicle), [
                'platform' => 'facebook',
            ]);

        $response->assertRedirect();
        $target = $response->headers->get('Location');
        $this->assertStringStartsWith('https://www.facebook.com/sharer/sharer.php?', $target);

        parse_str((string) parse_url($target, PHP_URL_QUERY), $query);
        $publicUrl = $query['u'] ?? '';

        $this->assertStringContainsString(route('vehicles.show', $vehicle->slug), $publicUrl);
        $this->assertStringContainsString('utm_source=facebook', $publicUrl);
        $this->assertStringContainsString('utm_medium=social', $publicUrl);
        $this->assertStringContainsString('utm_campaign=vehicle-'.$vehicle->id.'-promotion-1', $publicUrl);
        $this->assertStringNotContainsString('0712345678', $target);
        $this->assertDatabaseHas('vehicle_promotions', [
            'vehicle_id' => $vehicle->id,
            'user_id' => $admin->id,
            'platform' => 'facebook',
        ]);
    }

    public function test_whatsapp_opens_with_a_prepared_public_vehicle_message_and_link(): void
    {
        $admin = $this->admin();
        $vehicle = $this->vehicle();

        $response = $this->actingAs($admin)
            ->post(route('admin.vehicles.promotions.store', $vehicle), [
                'platform' => 'whatsapp',
                'caption' => 'Great car: '.$vehicle->year.' '.$vehicle->make.' '.$vehicle->model,
            ]);

        $response->assertRedirect();
        $target = $response->headers->get('Location');
        $this->assertStringStartsWith('https://wa.me/?text=', $target);

        parse_str((string) parse_url($target, PHP_URL_QUERY), $query);
        $message = rawurldecode($query['text'] ?? '');

        $this->assertStringContainsString($vehicle->make.' '.$vehicle->model, $message);
        $this->assertStringContainsString('utm_source=whatsapp', $message);
        $this->assertStringNotContainsString('0712345678', $message);
        $this->assertStringNotContainsString('Private Seller', $message);
    }

    public function test_instagram_and_tiktok_are_prepared_for_manual_publication(): void
    {
        $admin = $this->admin();
        $vehicle = $this->vehicle();

        foreach (['instagram', 'tiktok'] as $platform) {
            $response = $this->actingAs($admin)
                ->post(route('admin.vehicles.promotions.store', $vehicle), [
                    'platform' => $platform,
                ]);

            $response->assertRedirect(route('admin.vehicles.promote', $vehicle))
                ->assertSessionHas('prepared_platform', $platform)
                ->assertSessionHas('prepared_caption', fn (string $caption): bool => str_contains($caption, 'utm_source='.$platform)
                    && ! str_contains($caption, '0712345678')
                );
        }

        $this->assertDatabaseCount('vehicle_promotions', 2);
    }

    public function test_tracked_visits_and_inquiries_are_recorded_for_the_promotion(): void
    {
        $admin = $this->admin();
        $vehicle = $this->vehicle();

        $response = $this->actingAs($admin)
            ->post(route('admin.vehicles.promotions.store', $vehicle), [
                'platform' => 'facebook',
            ]);

        parse_str((string) parse_url($response->headers->get('Location'), PHP_URL_QUERY), $query);
        parse_str((string) parse_url($query['u'] ?? '', PHP_URL_QUERY), $campaign);

        $publicUrl = route('vehicles.show', $vehicle->slug);
        $this->get($publicUrl.'?'.http_build_query($campaign))
            ->assertOk()
            ->assertSee('vehicle_promotion_id')
            ->assertDontSee('0712345678');

        $this->get($publicUrl.'?'.http_build_query($campaign))->assertOk();
        $this->assertDatabaseCount('vehicle_promotion_visits', 1);

        $promotion = $vehicle->promotions()->firstOrFail();
        $this->post(route('inquiries.store'), [
            'vehicle_id' => $vehicle->id,
            'customer_name' => 'Buyer',
            'customer_phone' => '0700000000',
            'message' => 'I would like to view this vehicle.',
            'privacy_consent' => '1',
            'vehicle_promotion_id' => $promotion->id,
            'utm_source' => 'facebook',
            'utm_medium' => 'social',
            'utm_campaign' => 'vehicle-'.$vehicle->id.'-promotion-'.$promotion->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('inquiries', [
            'vehicle_id' => $vehicle->id,
            'vehicle_promotion_id' => $promotion->id,
            'utm_source' => 'facebook',
            'utm_medium' => 'social',
            'utm_campaign' => 'vehicle-'.$vehicle->id.'-promotion-'.$promotion->id,
        ]);
        $this->assertSame(1, Inquiry::where('vehicle_promotion_id', $promotion->id)->count());
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    private function vehicle(array $attributes = []): Vehicle
    {
        return Vehicle::create(array_merge([
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'price' => 1850000,
            'location' => 'Nairobi',
            'slug' => 'toyota-corolla-'.uniqid(),
            'status' => 'published',
            'seller_name' => 'Private Seller',
            'seller_phone' => '0712345678',
            'marketing_authorized_at' => now(),
        ], $attributes));
    }
}
