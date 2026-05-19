<?php

namespace App\Services;

use App\Models\Application;

class ApplicationService
{
    public function __construct(private Application $model) {}

    public function getApplicationsByPlan(?string $plan)
    {
        return $this->model->query()
            ->with(['customer', 'plan'])
            ->when($plan, function ($query, $plan) {
                $query->whereHas('plan', fn ($query) => $query->where('type', $plan));
            })->orderBy('created_at', 'asc');
    }
}
