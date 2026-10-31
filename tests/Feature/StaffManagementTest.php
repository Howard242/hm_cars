<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Inquiry;
use App\Models\InquiryFollowUp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_suspend_reactivate_and_remove_staff_access(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true, 'is_staff' => true])->save();
        $this->assertTrue($admin->fresh()->is_admin);
        $this->assertTrue($admin->fresh()->is_active);
        $staff = User::factory()->create();
        $staff->forceFill(['is_staff' => true])->save();
        $this->assertTrue($admin->fresh()->is_staff);

        $this->actingAs($admin);
        $this->assertSame($admin->id, auth()->id());
        $this->assertTrue(auth()->user()->is_admin);
        $this->get(route('admin.staff.index'))
            ->assertOk()
            ->assertSee('Suspend')
            ->assertSee('Remove access')
            ->assertSee('Delete account');

        $this->patch(route('admin.staff.suspend', $staff))
            ->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'is_staff' => true,
            'is_active' => false,
        ]);

        $this->patch(route('admin.staff.reactivate', $staff))
            ->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'is_staff' => true,
            'is_active' => true,
        ]);

        $this->delete(route('admin.staff.remove', $staff))
            ->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'is_staff' => false,
        ]);
    }

    public function test_admin_can_delete_a_staff_account_without_deleting_follow_up_history(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $staff = User::factory()->create();
        $staff->forceFill(['is_staff' => true])->save();
        $inquiry = Inquiry::create([
            'customer_name' => 'Test Customer',
            'customer_phone' => '0717000000',
            'message' => 'Interested in a vehicle.',
            'assigned_to_user_id' => $staff->id,
        ]);
        $followUp = InquiryFollowUp::create([
            'inquiry_id' => $inquiry->id,
            'user_id' => $staff->id,
            'contact_method' => 'Phone',
            'notes' => 'Discussed the vehicle details.',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.staff.destroy', $staff))
            ->assertRedirect(route('admin.staff.index'));

        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'assigned_to_user_id' => null,
        ]);
        $this->assertDatabaseHas('inquiry_follow_ups', [
            'id' => $followUp->id,
            'user_id' => null,
            'notes' => 'Discussed the vehicle details.',
        ]);
    }

    public function test_suspended_staff_cannot_log_in_or_continue_using_an_existing_session(): void
    {
        $staff = User::factory()->create([
            'password' => 'Staff-Password-123!',
        ]);
        $staff->forceFill(['is_staff' => true, 'is_active' => false])->save();

        $this->post(route('admin.login.submit'), [
            'email' => $staff->email,
            'password' => 'Staff-Password-123!',
        ])->assertSessionHasErrors('email');

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_non_admin_staff_cannot_manage_staff_accounts(): void
    {
        $staff = User::factory()->create();
        $staff->forceFill(['is_staff' => true])->save();

        $this->actingAs($staff)
            ->get(route('admin.staff.index'))
            ->assertForbidden();
    }
}
