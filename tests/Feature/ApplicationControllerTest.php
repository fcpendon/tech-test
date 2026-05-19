<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Override;
use Tests\TestCase;

class ApplicationControllerTest extends TestCase
{
    use RefreshDatabase;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_is_unauthenticated(): void
    {
        $response = $this->getJson('api/applications');

        $response->assertStatus(401);
    }

    public function test_user_is_authenticated(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('api/applications/');

        $response->assertStatus(200);
    }

    public function test_has_no_applications(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('api/applications/');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);
    }

    public function test_has_applications_with_plan_all(): void
    {
        Application::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('api/applications/');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'app_id',
                        'customer_full_name',
                        'address',
                        'state',
                        'plan_type',
                        'plan_name',
                        'plan_cost',
                    ]
                ],
                'links',
                'meta',
            ])->assertJsonCount(10, 'data');
    }

    public function test_has_applications_with_plan_nbn(): void
    {
        Application::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('api/applications/nbn');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'app_id',
                        'customer_full_name',
                        'address',
                        'state',
                        'plan_type',
                        'plan_name',
                        'plan_cost',
                    ]
                ],
                'links',
                'meta',
            ])->assertJsonPath('data.0.plan_type', 'nbn');
    }

    public function test_has_applications_with_plan_opticomm(): void
    {
        Application::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('api/applications/opticomm');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'app_id',
                        'customer_full_name',
                        'address',
                        'state',
                        'plan_type',
                        'plan_name',
                        'plan_cost',
                    ]
                ],
                'links',
                'meta',
            ])->assertJsonPath('data.0.plan_type', 'opticomm');
    }

    public function test_has_applications_with_plan_mobile(): void
    {
        Application::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('api/applications/mobile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'app_id',
                        'customer_full_name',
                        'address',
                        'state',
                        'plan_type',
                        'plan_name',
                        'plan_cost',
                    ]
                ],
                'links',
                'meta',
            ])->assertJsonPath('data.0.plan_type', 'mobile');
    }

    
    public function test_has_no_applications_with_plan_invalid(): void
    {
        Application::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('api/applications/invalid');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);
    }
}
