<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PowerBiService
{
    protected $clientId;
    protected $clientSecret;
    protected $tenantId;
    protected $workspaceId;
    protected $reportId;

    public function __construct()
    {
        $this->clientId = config('services.powerbi.client_id');
        $this->clientSecret = config('services.powerbi.client_secret');
        $this->tenantId = config('services.powerbi.tenant_id');
        $this->workspaceId = config('services.powerbi.workspace_id');
        $this->reportId = config('services.powerbi.report_id');
    }

    public function getAccessToken()
    {
        return Cache::remember('powerbi_access_token', 3300, function () {
            $response = Http::asForm()->post("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token", [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => 'https://analysis.windows.net/powerbi/api/.default',
            ]);

            return $response->json()['access_token'];
        });
    }

    public function getEmbedConfig($username = null, $roles = null)
    {
        $accessToken = $this->getAccessToken();

        // 1. Get Report Metadata first to get Dataset ID
        $reportResponse = Http::withToken($accessToken)
            ->get("https://api.powerbi.com/v1.0/myorg/groups/{$this->workspaceId}/reports/{$this->reportId}");

        if ($reportResponse->failed()) {
            throw new \Exception("Failed to fetch report details: " . $reportResponse->body());
        }

        $reportData = $reportResponse->json();

        // Use the API-provided URL to preserve query params (like &config=... for clusterUrl)
        $embedUrl = $reportData['embedUrl'];

        // Safety check: Ensure hostname is correct (fix api-api if it ever appears, but keep params)
        if (strpos($embedUrl, 'https://api-api.powerbi.com') === 0) {
            $embedUrl = str_replace('https://api-api.powerbi.com', 'https://app.powerbi.com', $embedUrl);
        }

        $datasetId = $reportData['datasetId'];

        // 2. Prepare Generate Token Request
        $tokenRequestData = [
            'accessLevel' => 'view',
            'datasetId' => $datasetId
        ];

        // Add RLS Identity if configured
        $rlsUsername = $username ?? config('services.powerbi.rls_username');
        $rlsRoles = $roles ?? config('services.powerbi.rls_roles');

        if ($rlsUsername && $rlsRoles) {
            $tokenRequestData['identities'] = [
                [
                    'username' => $rlsUsername,
                    'roles' => is_array($rlsRoles) ? $rlsRoles : explode(',', $rlsRoles),
                    'datasets' => [$datasetId]
                ]
            ];
        }

        // 3. Generate Embed Token
        $tokenResponse = Http::withToken($accessToken)
            ->post("https://api.powerbi.com/v1.0/myorg/groups/{$this->workspaceId}/reports/{$this->reportId}/GenerateToken", $tokenRequestData);

        if ($tokenResponse->failed()) {
            throw new \Exception("Failed to generate embed token: " . $tokenResponse->body());
        }

        $embedToken = $tokenResponse->json()['token'];

        return [
            'accessToken' => $embedToken,
            'embedUrl' => $embedUrl,
            'id' => $this->reportId,
            'tokenType' => 1, // Embed
            'expiration' => now()->addMinutes(45)->timestamp * 1000 // For client-side reference if needed
        ];
    }

    public function getReportPages()
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->get("https://api.powerbi.com/v1.0/myorg/groups/{$this->workspaceId}/reports/{$this->reportId}/pages");

        if ($response->failed()) {
            return [];
        }

        return $response->json()['value'];
    }
}
