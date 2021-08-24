<?php

namespace GrantHolle\OrangeHrm\Tests;

use GrantHolle\OrangeHrm\OrangeHrm;
use Illuminate\Support\Arr;

class OrangeHrmTest extends TestCase
{
    protected OrangeHrm $orangeHrm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orangeHrm = $this->app->make(OrangeHrm::class);
    }

    public function test_service_container_injection_working_correctly()
    {
        $this->assertInstanceOf(OrangeHrm::class, $this->orangeHrm);
    }

    public function test_can_list_employees()
    {
        $employees = $this->orangeHrm->getEmployees();

        $this->assertArrayHasKey('data', $employees);
        $this->assertArrayHasKey('meta', $employees);
    }

    public function test_can_list_employees_with_parameters()
    {
        $employees = $this->orangeHrm->getEmployees([
            'include' => 'supervisors,jobTitle',
        ]);

        $this->assertArrayHasKey('data', $employees);
        $this->assertArrayHasKey('meta', $employees);
        $this->assertTrue(Arr::has($employees, 'data.0.supervisors'));
        $this->assertTrue(Arr::has($employees, 'data.0.jobTitle'));
    }

    public function test_can_get_individual_employee()
    {
        $employee = $this->orangeHrm->getEmployee(1);

        $this->assertTrue(Arr::has($employee, [
            'data.empNumber',
            'data.firstName',
            'data.lastName',
        ]));
    }
}
