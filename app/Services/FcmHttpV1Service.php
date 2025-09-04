<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // <-- إضافة مهمة لاستخدام نظام السجلات

class FcmHttpV1Service
{
    protected string $projectId;
    protected string $credentialsPath;

    public function __construct()
    {
        $this->projectId       = config('services.fcm_v1.project_id');
        $this->credentialsPath = config('services.fcm_v1.credentials');
    }

    protected function accessToken(): string
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $creds  = new ServiceAccountCredentials($scopes, $this->credentialsPath);
        $token  = $creds->fetchAuthToken();

        return $token['access_token'];
    }

    public function sendToToken(string $token, array $notification = [], array $data = []): array
    {
        $payload = [
            'message' => array_filter([
                'token'        => $token,
                'notification' => $notification ?: null,
                'data'         => $data ? array_map('strval', $data) : null,
                'android'      => [
                    'priority'     => 'HIGH',
                    'notification' => [
                        'channel_id' => 'default',
                        'sound'      => 'default',
                    ],
                ],
            ]),
        ];

        // =======================>> التعديل الأول: تسجيل ما يتم إرساله <<=======================
        Log::info('[FCMv1] Sending Push Notification Request:', ['token' => $token, 'payload' => $payload]);
        // ===================================================================================

        $url  = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        $resp = Http::withToken($this->accessToken())->post($url, $payload);

        // =======================>> التعديل الثاني: تسجيل الرد المستلم بالكامل <<=======================
        Log::info('[FCMv1] Received Response from Google:', [
            'status' => $resp->status(),
            'body' => $resp->json(), // .json() أفضل لعرض الرد بشكل منظم في السجل
            'successful' => $resp->successful(),
        ]);
        // ========================================================================================

        if (!$resp->successful()) {
            // لا تغيير هنا، الكود الأصلي كان ممتازًا في تسجيل الخطأ
            throw new \RuntimeException('FCM v1 error: '.$resp->status().' '.$resp->body());
        }

        return $resp->json();
    }

    public function sendToTokens(array $tokens, array $notification = [], array $data = []): array
    {
        $out = [];
        foreach ($tokens as $t) {
            try {
                $out[$t] = $this->sendToToken($t, $notification, $data);
            } catch (\Throwable $e) {
                // سيتم تسجيل الخطأ الآن بشكل تفصيلي من دالة sendToToken
                $out[$t] = ['error' => $e->getMessage()];
            }
        }
        return $out;
    }
}