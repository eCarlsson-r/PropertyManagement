<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;

trait InteractWithVertexAI
{
    /**
     * Helper to get OAuth2 token for Vertex AI
     */
    protected function getGoogleAccessToken()
    {
        // 1. Local Development Shortcut
        if (app()->environment('local')) {
            $token = shell_exec('/Users/carlsson/Downloads/google-cloud-sdk/bin/gcloud auth print-access-token');
            return trim($token);
        }
        
        // 2. Production / Service Account Method
        // Ensure your service account JSON is stored securely
        $keyPath = storage_path('app/google-credentials.json');
        
        if (!file_exists($keyPath)) {
            throw new \Exception("Service account credentials missing at {$keyPath}");
        }

        $auth = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/cloud-platform',
            $keyPath
        );

        return $auth->fetchAuthToken()['access_token'];
    }

    /**
     * Standardized Vertex AI Request Wrapper
     */
    protected function postToVertex($endpoint, $payload)
    {
        $projectId = config('services.google.project_id');
        $location = config('services.google.location', 'asia-southeast1');
        
        $url = "https://{$location}-aiplatform.googleapis.com/v1/projects/{$projectId}/locations/{$location}/publishers/google/models/{$endpoint}";

        return Http::withToken($this->getGoogleAccessToken())
            ->post($url, $payload);
    }
}