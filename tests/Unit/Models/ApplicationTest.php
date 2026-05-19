<?php

namespace Tests\Unit\Models;

use App\Models\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function test_address_accessor(): void
    {
        $application = new Application();
        $application->address_1 = 'address 1';
        $application->address_2 = 'address 2';
        $application->city = 'city';
        $application->postcode = 'postcode';

        $expected_address = "{$application->address_1} {$application->address_2} {$application->city} {$application->postcode}";

        $this->assertEquals($expected_address, $application->address);
    }
}
