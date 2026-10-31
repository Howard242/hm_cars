<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleSubmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_open_the_vehicle_submission_form(): void
    {
        $response = $this->get(route('sell.create'));

        $response->assertOk();
        $response->assertSee('Sell your car');
    }
}
