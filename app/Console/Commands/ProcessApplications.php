<?php

namespace App\Console\Commands;

use App\Enums\ApplicationStatus;
use App\Jobs\ProcessApplication;
use Illuminate\Console\Command;
use App\Models\Application;

class ProcessApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'applications:process {status} {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process applications with a specific status and plan type';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = $this->argument('status');
        $type = $this->argument('type');

        if (!ApplicationStatus::tryFrom($status)) {
            $this->fail("Invalid status '{$status}' provided");
        }

        $applications = Application::query()
            ->with('plan')
            ->where('status', $status)
            ->whereHas('plan', fn ($query) => $query->where('type', $type))
            ->get();

        // dd($applications);

        if ($applications->isEmpty()) {
            $this->info("No applications of status '{$status}' and plan '{$type}' found");
            return Command::SUCCESS;
        }

        $this->info("Queueing {$applications->count()} applications of status '{$status}' and plan '{$type}'");

        foreach ($applications as $application) {
            ProcessApplication::dispatch($application);
        }
    }
}
