<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\InquiryFollowUp;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_creates_a_configured_admin_account(): void
    {
        config([
            'hmcars.admin.name' => 'HM CARS Admin',
            'hmcars.admin.email' => 'admin-seed@example.test',
            'hmcars.admin.password' => 'Temporary-Test-Password-242!',
        ]);

        $this->seed(AdminUserSeeder::class);

        $admin = User::where('email', 'admin-seed@example.test')->firstOrFail();

        $this->assertTrue($admin->is_admin);
        $this->assertTrue($admin->is_staff);
        $this->assertTrue(Hash::check('Temporary-Test-Password-242!', $admin->password));
    }

    public function test_admin_dashboard_summarizes_vehicle_and_inquiry_activity(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill([
            'is_admin' => true,
            'is_staff' => true,
        ])->save();

        $availableVehicle = Vehicle::create([
            'make' => 'Toyota',
            'model' => 'Fielder',
            'year' => 2025,
            'price' => 1250000,
            'location' => 'Nakuru',
            'slug' => 'dashboard-available-vehicle',
            'status' => 'published',
        ]);

        $soldVehicle = Vehicle::create([
            'make' => 'Mazda',
            'model' => 'Demio',
            'year' => 2017,
            'price' => 780000,
            'location' => 'Mombasa',
            'slug' => 'dashboard-sold-vehicle',
            'status' => 'published',
        ]);

        Vehicle::create([
            'make' => 'Honda',
            'model' => 'Vezel',
            'year' => 2020,
            'price' => 2350000,
            'location' => 'Kisumu',
            'slug' => 'dashboard-pending-vehicle',
            'status' => 'pending',
        ]);

        $newInquiry = Inquiry::create([
            'vehicle_id' => $availableVehicle->id,
            'customer_name' => 'New Customer',
            'customer_phone' => '0717000001',
            'message' => 'Interested in this vehicle.',
            'status' => 'New',
        ]);

        Inquiry::create([
            'vehicle_id' => $soldVehicle->id,
            'customer_name' => 'Completed Customer',
            'customer_phone' => '0717000002',
            'message' => 'Purchase completed.',
            'status' => 'Sold',
        ]);

        Inquiry::create([
            'vehicle_id' => null,
            'customer_name' => 'Follow-up Customer',
            'customer_phone' => '0717000003',
            'message' => 'Looking for a car.',
            'status' => 'Contacted',
        ]);

        InquiryFollowUp::create([
            'inquiry_id' => $newInquiry->id,
            'user_id' => $admin->id,
            'contact_method' => 'Phone',
            'notes' => 'Call tomorrow.',
            'next_follow_up_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertViewHas('totalVehicles', 3)
            ->assertViewHas('availableVehicles', 1)
            ->assertViewHas('soldVehicles', 1)
            ->assertViewHas('recentVehicleCount', 3)
            ->assertViewHas('totalInquiries', 3)
            ->assertViewHas('newInquiries', 1)
            ->assertViewHas('awaitingFollowUp', 1)
            ->assertViewHas('recentInquiries', fn ($inquiries) => $inquiries->count() === 3)
            ->assertViewHas('recentVehicles', fn ($vehicles) => $vehicles->count() === 3)
            ->assertSee('Operations Overview')
            ->assertSee('Recent Customer Inquiries')
            ->assertSee('Recent Vehicle Listings')
            ->assertSee('Edit seller agreement');
    }
}
