<?php

namespace App\Http\Resources;

use App\Enums\ApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'app_id'             => $this->id,
            'customer_full_name' => $this->customer->full_name,
            'address'            => $this->address,
            'state'              => $this->state,
            'plan_type'          => $this->plan->type,
            'plan_name'          => $this->plan->name,
            'plan_cost'          => $this->plan->monthly_cost,
            'order_id'           => $this->when($this->status === ApplicationStatus::Complete, $this->order_id),
        ];
    }
}
