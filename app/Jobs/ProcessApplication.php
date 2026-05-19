<?php

namespace App\Jobs;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ProcessApplication implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Application $application) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $endpoint = env('NBN_B2B_ENDPOINT');

        try {
            $response = Http::post($endpoint, [
                'address_1' => $this->application->address_1,
                'address_2' => $this->application->address_2,
                'city'      => $this->application->city,
                'state'     => $this->application->state,
                'postcode'  => $this->application->postcode,
                'plan_name' => $this->application->plan->name,
            ]);

            if ($response->successful() && $response->json('status') === 'Successful') {
                $this->application->update([
                    'order_id' => $response->json('id'),
                    'status'   => ApplicationStatus::Complete,
                ]);

                return;
            }

            throw new Exception;
        } catch (Exception $e) {
            $this->application->update(['status' => ApplicationStatus::OrderFailed]);
        }
    }
}
