<?php

namespace App\Services;

use App\Models\FcmToken;
use App\Models\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    /**
     * Get the Firebase credentials array.
     */
    protected function getCredentials(): ?array
    {
        $jsonStr = config('firebase.credentials_json');

        if ($jsonStr) {
            $decoded = json_decode($jsonStr, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        $path = config('firebase.credentials_path');
        if ($path) {
            $fullPath = base_path($path);
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }
        }

        return null;
    }

    /**
     * Generate the OAuth2 access token for FCM V1.
     */
    public function getAccessToken(): ?string
    {
        $credentials = $this->getCredentials();

        if (!$credentials || empty($credentials['private_key']) || empty($credentials['client_email'])) {
            return null;
        }

        return Cache::remember('firebase_oauth_access_token', 3300, function () use ($credentials) {
            $privateKey = str_replace('\n', "\n", $credentials['private_key']);
            
            $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
            $now = time();
            $payload = json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]);

            $base64UrlHeader = $this->base64UrlEncode($header);
            $base64UrlPayload = $this->base64UrlEncode($payload);

            $signature = '';
            $success = openssl_sign(
                $base64UrlHeader . "." . $base64UrlPayload,
                $signature,
                $privateKey,
                'SHA256'
            );

            if (!$success) {
                Log::error('FirebaseService: Failed to sign JWT with private key.');
                throw new \Exception('Failed to sign Google OAuth JWT assertion.');
            }

            $base64UrlSignature = $this->base64UrlEncode($signature);
            $jwtAssertion = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwtAssertion,
            ]);

            if ($response->failed()) {
                Log::error('FirebaseService: Failed to fetch Google OAuth token.', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Failed to obtain Google OAuth access token.');
            }

            return $response->json()['access_token'] ?? null;
        });
    }

    /**
     * Send push notification to a specific FCM token.
     */
    public function sendMessage(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        $credentials = $this->getCredentials();
        $projectId = config('firebase.project_id') ?: ($credentials['project_id'] ?? null);
        $accessToken = $this->getAccessToken();

        // Check if we should fall back to mock logging mode
        if (!$projectId || !$accessToken) {
            Log::info('FirebaseService (MOCK SEND): Push notification simulated.', [
                'fcm_token' => $fcmToken,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'reason' => 'Firebase credentials or project ID not configured.'
            ]);
            return true;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $message = [
            'token' => $fcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];

        if (!empty($data)) {
            // FCM v1 requires all values in data to be strings
            $stringData = [];
            foreach ($data as $key => $val) {
                $stringData[(string) $key] = is_array($val) ? json_encode($val) : (string) $val;
            }
            $message['data'] = $stringData;
        }

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->contentType('application/json')
            ->post($url, ['message' => $message]);

        if ($response->successful()) {
            Log::info('FirebaseService: Notification sent successfully.', [
                'fcm_token' => $fcmToken,
                'response' => $response->json()
            ]);
            return true;
        }

        // Standard FCM responses for invalid/expired tokens (e.g. UNREGISTERED / 404 / 410)
        // If the token is unregistered or bad, clean it up from our database.
        $status = $response->status();
        $responseBody = $response->json();
        
        Log::warning('FirebaseService: FCM send failed.', [
            'status' => $status,
            'body' => $responseBody,
            'fcm_token' => $fcmToken
        ]);

        $errorCode = $responseBody['error']['status'] ?? '';
        if ($status === 404 || $status === 410 || $errorCode === 'UNREGISTERED' || $errorCode === 'RESOURCE_EXHAUSTED') {
            Log::info("FirebaseService: Invalid/expired token detected. Deleting token from database.", [
                'fcm_token' => $fcmToken
            ]);
            FcmToken::where('fcm_token', $fcmToken)->delete();
        }

        return false;
    }

    /**
     * Send push notification to a user (to all registered tokens).
     */
    public function sendToUser($user, string $title, string $body, array $data = []): array
    {
        $userIds = [$user->id];

        // Retrieve linked user IDs based on shared admission mobile or email
        $admission = $user->admission;
        if ($admission) {
            $query = \App\Models\Admission::query();
            if ($admission->mobile) {
                $query->orWhere('mobile', $admission->mobile);
            }
            if ($admission->email) {
                $query->orWhere('email', $admission->email);
            }
            $linkedUserIds = $query->pluck('user_id')->filter()->toArray();
            $userIds = array_unique(array_merge($userIds, $linkedUserIds));
        }

        // Store in database notifications history table for each user account
        foreach ($userIds as $id) {
            Notification::create([
                'user_id' => $id,
                'title' => $title,
                'message' => $body,
                'data' => $data,
                'is_read' => false,
            ]);
        }

        $tokens = FcmToken::whereIn('user_id', $userIds)->pluck('fcm_token')->toArray();

        if (empty($tokens)) {
            Log::info('FirebaseService: User and linked accounts have no registered FCM tokens.', [
                'user_ids' => $userIds
            ]);
            return [];
        }

        $results = [];
        foreach ($tokens as $token) {
            $results[$token] = $this->sendMessage($token, $title, $body, $data);
        }

        return $results;
    }

    /**
     * Helper to base64Url encode string.
     */
    protected function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
