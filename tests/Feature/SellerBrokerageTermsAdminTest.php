<?php

namespace Tests\Feature;

use App\Models\SellerBrokerageTerms;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerBrokerageTermsAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_publish_a_new_seller_terms_version(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)
            ->get(route('admin.seller-terms.edit'))
            ->assertOk()
            ->assertSee('Seller Brokerage Agreement');

        $this->actingAs($admin)
            ->put(route('admin.seller-terms.update'), [
                'current_version' => SellerBrokerageTerms::current()->version,
                'title' => 'Updated seller brokerage agreement',
                'introduction' => 'Updated introduction for the brokerage agreement.',
                'clauses' => [
                    ['title' => 'Updated first clause', 'body' => 'First updated clause body.'],
                    ['title' => 'Updated second clause', 'body' => 'Second updated clause body.'],
                ],
            ])
            ->assertRedirect(route('admin.seller-terms.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('seller_brokerage_terms_versions', [
            'version' => '1.1',
            'title' => 'Updated seller brokerage agreement',
        ]);
    }
}
