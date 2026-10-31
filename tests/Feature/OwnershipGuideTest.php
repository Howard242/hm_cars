<?php

namespace Tests\Feature;

use App\Models\OwnershipGuide;
use App\Models\OwnershipGuideStep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnershipGuideTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_sign_in_without_the_staff_flag(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->post(route('admin.login.submit'), [
            'email' => $admin->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);

        $this->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_authenticated_admin_visiting_login_is_redirected_to_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)
            ->get(route('admin.login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_customers_can_view_the_published_guide_and_homepage_links_to_it(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('ownership-guide.show'))
            ->assertSee('How to own a car');

        $this->get(route('ownership-guide.show'))
            ->assertOk()
            ->assertSee('How to Own a Car in Kenya')
            ->assertSee('Complete the official ownership transfer')
            ->assertSee('not legal or financial advice');
    }

    public function test_admin_can_manage_preview_and_publish_guide_content(): void
    {
        $guide = OwnershipGuide::with('steps')->firstOrFail();
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $staff = User::factory()->create();
        $staff->forceFill(['is_staff' => true])->save();

        $this->actingAs($staff)
            ->get(route('admin.ownership-guide.edit'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.ownership-guide.edit'))
            ->assertOk()
            ->assertSee('Page settings')
            ->assertSee('Add a step');

        $step = $guide->steps->firstOrFail();
        $this->actingAs($admin)
            ->put(route('admin.ownership-guide.steps.update', $step), [
                'title' => 'Check the vehicle details',
                'body' => 'Confirm the records with official services.',
                'sort_order' => 2,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.ownership-guide.edit'));

        $this->actingAs($admin)
            ->post(route('admin.ownership-guide.steps.store'), [
                'title' => 'A private draft',
                'body' => 'This step is hidden until published.',
                'sort_order' => 0,
                'is_published' => '0',
            ])
            ->assertRedirect(route('admin.ownership-guide.edit'));

        $this->get(route('ownership-guide.show'))
            ->assertOk()
            ->assertSee('Check the vehicle details')
            ->assertDontSee('A private draft');

        $this->put(route('admin.ownership-guide.update'), [
            'title' => 'Updated ownership guide',
            'introduction' => 'Updated introduction.',
        ])->assertRedirect(route('admin.ownership-guide.edit'));

        $this->get(route('ownership-guide.show'))->assertNotFound();
        $this->get(route('admin.ownership-guide.preview'))
            ->assertOk()
            ->assertSee('Admin preview')
            ->assertSee('A private draft');
    }

    public function test_admin_can_upload_and_remove_a_step_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $step = OwnershipGuide::firstOrFail()->steps()->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.ownership-guide.steps.update', $step), [
                'title' => $step->title,
                'body' => $step->body,
                'sort_order' => $step->sort_order,
                'is_published' => '1',
                'image' => $this->tinyPngUpload(),
            ])
            ->assertRedirect(route('admin.ownership-guide.edit'));

        $step->refresh();
        $this->assertNotNull($step->image_path);
        $imagePath = $step->image_path;
        Storage::disk('public')->assertExists($imagePath);

        $this->actingAs($admin)
            ->put(route('admin.ownership-guide.steps.update', $step), [
                'title' => $step->title,
                'body' => $step->body,
                'sort_order' => $step->sort_order,
                'is_published' => '1',
                'remove_image' => '1',
            ])
            ->assertRedirect(route('admin.ownership-guide.edit'));

        $step->refresh();
        $this->assertNull($step->image_path);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_step_delete_removes_its_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $path = 'ownership-guide/test-image.png';
        Storage::disk('public')->put($path, 'test image');
        $step = OwnershipGuide::firstOrFail()->steps()->create([
            'title' => 'Temporary step',
            'body' => 'Temporary step details.',
            'image_path' => $path,
            'sort_order' => 99,
            'is_published' => false,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.ownership-guide.steps.destroy', $step))
            ->assertRedirect(route('admin.ownership-guide.edit'));

        $this->assertDatabaseMissing('ownership_guide_steps', ['id' => $step->id]);
        Storage::disk('public')->assertMissing($path);
    }

    private function tinyPngUpload(): UploadedFile
    {
        $contents = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=',
            true
        );

        $this->assertNotFalse($contents);

        return UploadedFile::fake()->createWithContent('guide.png', $contents);
    }
}
