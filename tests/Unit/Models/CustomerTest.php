<?php

namespace Tests\Unit\Models;

use App\Models\Customer;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public function test_full_name_accessor(): void
    {
        $customer = new Customer();
        $customer->first_name = 'First';
        $customer->last_name = 'Last';

        $expected_full_name = "{$customer->first_name} {$customer->last_name}";

        $this->assertEquals($expected_full_name, $customer->full_name);
    }
}
