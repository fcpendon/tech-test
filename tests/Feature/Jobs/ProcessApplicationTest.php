<?php

namespace Tests\Feature\Jobs;

use App\Enums\ApplicationStatus;
use App\Jobs\ProcessApplication;
use App\Models\Application;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProcessApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_application_status_complete_when_successful(): void
    {
        $response = json_decode(file_get_contents(base_path('tests/stubs/nbn-successful-response.json')), true);
        Http::fake([
            env('NBN_B2B_ENDPOINT') => Http::response($response)
        ]);

        $application = Application::factory()->create([
            'status' => ApplicationStatus::Order,
            'plan_id' => Plan::factory()->create(['type' => 'nbn'])->id,
        ]);

        $job = new ProcessApplication($application);
        $job->handle();

        $this->assertEquals(ApplicationStatus::Complete, $application->status);
        $this->assertEquals($response['id'], $application->order_id);
    }

    public function test_process_application_status_order_failed_when_fail(): void
    {
        $response = json_decode(file_get_contents(base_path('tests/stubs/nbn-fail-response.json')), true);
        Http::fake([
            env('NBN_B2B_ENDPOINT') => Http::response($response)
        ]);

        $application = Application::factory()->create([
            'status' => ApplicationStatus::Order,
            'plan_id' => Plan::factory()->create(['type' => 'nbn'])->id,
        ]);

        $job = new ProcessApplication($application);
        $job->handle();

        $this->assertEquals(ApplicationStatus::OrderFailed, $application->status);
        $this->assertEquals(null, $application->order_id);
    }

    public function test_process_application_status_order_failed_when_500(): void
    {
        Http::fake([
            env('NBN_B2B_ENDPOINT') => Http::response('Internal Server Error', 500)
        ]);

        $application = Application::factory()->create([
            'status' => ApplicationStatus::Order,
            'plan_id' => Plan::factory()->create(['type' => 'nbn'])->id,
        ]);

        $job = new ProcessApplication($application);
        $job->handle();

        $this->assertEquals(ApplicationStatus::OrderFailed, $application->status);
        $this->assertEquals(null, $application->order_id);
    }
}
