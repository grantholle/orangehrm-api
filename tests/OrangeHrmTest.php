<?php

namespace GrantHolle\OrangeHrm\Tests;

use GrantHolle\OrangeHrm\OrangeHrm;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

class OrangeHrmTest extends TestCase
{
    use WithFaker;

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

    public function test_can_add_employee()
    {
        $data = [
            'firstName' => $this->faker->firstName,
            'middleName' => '',
            'lastName' => $this->faker->lastName,
            'chkLogin' => true,
            'userName' => $this->faker->userName,
            'userPassword' => 'password123',
            'rePassword' => 'password123',
            'status' => 'Enabled',
            'employeeId' => null,
            'locationId' => 1,
            'essRoleId' => 2,
            'supervisorRoleId' => 3,
            'photo' => [
                'filename' => 'bob.jpeg',
                'filesize' => filesize(__DIR__.'/bob.jpeg'),
                'filetype' => 'image/jpeg',
                'base64' => base64_encode(file_get_contents(__DIR__.'/bob.jpeg')),
            ],
        ];

        $results = $this->orangeHrm->addEmployee($data);

        $this->assertArrayHasKey('data', $results);
        $this->assertTrue(Arr::has($results, 'messages.success'));

        $this->assertNotNull($results['data']['empNumber']);
        $this->assertEquals($data['firstName'], $results['data']['firstName']);
        $this->assertEquals($data['lastName'], $results['data']['lastName']);
    }

    public function test_can_retrieve_locations()
    {
        $locations = $this->orangeHrm->getLocations();

        $this->assertArrayHasKey('data', $locations);
        $this->assertArrayHasKey('meta', $locations);
    }
}
