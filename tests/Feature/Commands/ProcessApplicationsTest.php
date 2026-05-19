<?php

namespace Tests\Feature\Commands;

use App\Enums\ApplicationStatus;
use App\Jobs\ProcessApplication;
use App\Models\Application;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessApplicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_zero_applications(): void
    {
        Queue::fake();

        $this->artisan('applications:process order nbn')
            ->expectsOutput("No applications of status 'order' and plan 'nbn' found")
            ->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_process_status_order_and_plan_nbn_no_matches(): void
    {
        Queue::fake();

        $other_apps = Application::factory()->count(10)->create();

        $this->artisan('applications:process order nbn')
            ->expectsOutput("No applications of status 'order' and plan 'nbn' found")
            ->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_process_status_order_and_plan_nbn_with_matches(): void
    {
        Queue::fake();

        $plan_nbn = Plan::factory()->create(['type' => 'nbn']);
        $other_apps = Application::factory()->count(10)->create();
        $target_apps = Application::factory()->count(3)->create([
            'status'  => ApplicationStatus::Order,
            'plan_id' => $plan_nbn->id,
        ]);
        $target_count = $target_apps->count();

        $this->artisan('applications:process order nbn')
            ->expectsOutput("Queueing {$target_count} applications of status 'order' and plan 'nbn'")
            ->assertSuccessful();

        Queue::assertPushed(ProcessApplication::class, $target_count);
    }
}
