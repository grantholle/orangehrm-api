<?php

namespace GrantHolle\OrangeHrm;

use GrantHolle\OrangeHrm\Exceptions\OrangeHrmApiException;
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

    protected $authAttempts = 0;

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

        $this->authAttempts++;

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

    protected function makeRequest(string $method, string $endpoint, array $data): array
    {
        /** @var Response $response */
        $response = $this->http()->$method($endpoint, $data);

        if ($response->status() === 401) {
            $error = $response->json();

            if (
                $this->authAttempts < 5 &&
                $error['error'] === 'invalid_token' ||
                $error['error'] === 'expired_token'
            ) {
                $this->setAccessToken(true);
                return $this->makeRequest($method, $endpoint, $data);
            }

            throw new OrangeHrmApiException("{$error['error']}: {$error['error_description']}");
        }

        return $response->json();
    }

    public function get(string $endpoint, array $query = []): array
    {
        return $this->makeRequest('get', $endpoint, $query);
    }

    public function patch(string $endpoint, array $data = []): array
    {
        return $this->makeRequest('patch', $endpoint, $data);
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->makeRequest('post', $endpoint, $data);
    }

    public function getEmployees(array $parameters = []): array
    {
        return $this->get('/api/employees', $parameters);
    }

    public function getEmployee($id): array
    {
        return $this->get("/api/employees/{$id}", [
            'include' => 'supervisors,subordinates,dependents,emergencyContacts,EmployeeImmigrationRecord,workExperience,education,skills,languages,EmployeeLicense,JobRecord,EmployeeTerminationRecord,SalaryRecord,SalaryHistoryRecord,EmployeeSalaryComponent,EmployeeMembership,DirectDepositRecord',
        ]);
    }

    public function getEmployeeCustomFields($id, ?string $screen = 'personal', ?string $module = 'pim'): ?array
    {
        return $this->get("/api/employees/{$id}/CustomFieldValue?filter[screen]={$screen}&filter[screen][module]={$module}");
    }

    public function addEmployee(array $data = []): array
    {
        return $this->post('/api/employees', $data);
    }

    public function updateEmployee(string|int $id, array $data = []): array
    {
        return $this->patch("/api/employees/{$id}", $data);
    }

    public function getLocations(array $parameters = []): array
    {
        return $this->get('/api/locations', $parameters);
    }

    public function getEmploymentStatuses(array $parameters = []): array
    {
        return $this->get('/api/employmentStatus', $parameters);
    }

    public function getJobTitles(array $parameters = []): array
    {
        return $this->get('/api/jobTitles', $parameters);
    }

    public function addJobTitle(array $data): array
    {
        return $this->post('/api/jobTitles', $data);
    }

    public function getNationalities(array $parameters = []): array
    {
        return $this->get('/api/nationality', $parameters);
    }

    public function getSubunits(array $parameters = []): array
    {
        return $this->get('/api/subunits', $parameters);
    }

    public function updateJobRecord(string|int $empNumber, array $data): array
    {
        return $this->patch("/api/employees/{$empNumber}/job", $data);
    }
}
