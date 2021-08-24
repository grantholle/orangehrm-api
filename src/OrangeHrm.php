<?php

namespace GrantHolle\OrangeHrm;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrangeHrm
{
    protected string $url;
    protected string $clientId;
    protected string $clientSecret;
    protected ?string $accessToken;
    const CACHE_KEY = 'orangehrm_access_token';

    public function __construct(string $url, string $clientId, string $clientSecret)
    {
        $this->url = $url;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = Cache::get(static::CACHE_KEY);
        ray($this->accessToken);
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

        $response = $this->post('/oauth/issueToken', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
        ]);
        $data = (object) $response->json();

        Cache::put(static::CACHE_KEY, $data->access_token, now()->addSeconds($data->expires_in));
        $this->accessToken = $data->access_token;
        ray('Setting access token', $this->accessToken);
    }

    protected function http(): PendingRequest
    {
        if (!$this->accessToken) {
            $this->setAccessToken(true);
        }

        return Http::baseUrl($this->url)
            ->withToken($this->accessToken)
            ->withOptions(['debug' => true]);
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

    public function getEmployee($id): array
    {
        $response = $this->get("/api/employees/{$id}", [
            'include' => 'supervisors,subordinates,dependents,emergencyContacts,EmployeeImmigrationRecord,workExperience,education,skills,languages,EmployeeLicense,JobRecord,EmployeeTerminationRecord,SalaryRecord,SalaryHistoryRecord,EmployeeSalaryComponent,EmployeeMembership,DirectDepositRecord',
        ]);

        return $response->json();
    }

    public function getEmployeeCustomFields($id, string $screen = 'personal', string $module = 'pim'):? array
    {
        $response = $this->http()
            ->get("/api/employees/{$id}/CustomFieldValue?filter[screen]={$screen}&filter[screen][module]={$module}");

        return $response->json();
    }

    public function updateEmployee($id, array $data = []): bool
    {
        $response = $this->patch("/api/employees/{$id}", $data);
        $successful = $response->successful();

        Log::error("Failed updating employee: {$response->body()}");

        return $successful;
    }

    public function addEmployee(array $data = []): bool
    {
        $response = $this->post('/api/employees', $data);

        return $response->successful();
    }
}
