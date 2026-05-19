<?php

namespace Tests\Unit\Models;

use App\Models\Plan;
use PHPUnit\Framework\TestCase;

class PlanTest extends TestCase
{
    public function test_monthly_cost_accessor(): void
    {
        $plan = new Plan();
        $plan->monthly_cost = 1234;

        $expected_monthly_cost = '$12.34';

        $this->assertEquals($expected_monthly_cost, $plan->monthly_cost);
    }
}
