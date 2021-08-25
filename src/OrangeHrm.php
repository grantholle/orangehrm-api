<?php

namespace GrantHolle\OrangeHrm;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OrangeHrm
{
    protected string $url;
    protected string $clientId;
    protected string $clientSecret;
    protected ?string $accessToken;
    public const CACHE_KEY = 'orangehrm_access_token';

    public function __construct(string $url, string $clientId, string $clientSecret)
    {
        $this->url = $url;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = Cache::get(static::CACHE_KEY);
    }

    public static function clearCache()
    {
        Cache::delete(static::CACHE_KEY);
    }

    protected function setAccessToken(bool $force = false): void
    {
        if (!$force && $this->accessToken) {
            return;
        }

        $response = Http::baseUrl($this->url)
            ->post('/oauth/issueToken', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials',
            ]);
        $data = (object) $response->json();

        Cache::put(static::CACHE_KEY, $data->access_token, now()->addSeconds($data->expires_in));
        $this->accessToken = $data->access_token;
    }

    protected function http(): PendingRequest
    {
        if (!$this->accessToken) {
            $this->setAccessToken(true);
        }

        return Http::baseUrl($this->url)
            ->withToken($this->accessToken)
            ->withOptions(['debug' => false]);
    }

    public function get(string $endpoint, array $query = []): Response
    {
        return $this->http()->get($endpoint, $query);
    }

    public function patch(string $endpoint, array $data = []): Response
    {
        return $this->http()->patch($endpoint, $data);
    }

    public function post(string $endpoint, array $data = []): Response
    {
        return $this->http()->post($endpoint, $data);
    }

    public function getEmployees(array $parameters = []): array
    {
        return $this->get('/api/employees', $parameters)
            ->json();
    }

    public function getEmployee($id): array
    {
        return $this->get("/api/employees/{$id}", [
            'include' => 'supervisors,subordinates,dependents,emergencyContacts,EmployeeImmigrationRecord,workExperience,education,skills,languages,EmployeeLicense,JobRecord,EmployeeTerminationRecord,SalaryRecord,SalaryHistoryRecord,EmployeeSalaryComponent,EmployeeMembership,DirectDepositRecord',
        ])->json();
    }

    public function getEmployeeCustomFields($id, ?string $screen = 'personal', ?string $module = 'pim'): ?array
    {
        return $this->http()
            ->get("/api/employees/{$id}/CustomFieldValue?filter[screen]={$screen}&filter[screen][module]={$module}")
            ->json();
    }

    public function addEmployee(array $data = []): array
    {
        return $this->post('/api/employees', $data)
            ->json();
    }

    public function updateEmployee($id, array $data = []): array
    {
        return $this->patch("/api/employees/{$id}", $data)
            ->json();
    }

    public function getLocations(array $parameters = []): array
    {
        return $this->get('/api/locations', $parameters)
            ->json();
    }

    public function getEmploymentStatuses(array $parameters = []): array
    {
        return $this->get('/api/employmentStatus', $parameters)
            ->json();
    }

    public function getJobTitles(array $parameters = []): array
    {
        return $this->get('/api/jobTitles', $parameters)
            ->json();
    }

    public function addJobTitle(array $data): array
    {
        return $this->post('/api/jobTitles', $data)
            ->json();
    }
}
