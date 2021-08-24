<?php

namespace GrantHolle\OrangeHrm\Tests;

use GrantHolle\OrangeHrm\OrangeHrm;

class OrangeHrmTest extends TestCase
{
    public function test_service_container_injection_working_correctly()
    {
        $this->assertInstanceOf(OrangeHrm::class, $this->app->make(OrangeHrm::class));
    }
}
