<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected function getAccessToken()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/firebase.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $token = $client->fetchAccessTokenWithAssertion();

    \Log::info('FCM Token:', [$token]);
        return $token['access_token'];
    }

    public function sendNotification($tokens, $title, $body)
    {
        $accessToken = $this->getAccessToken();

        $url = "https://fcm.googleapis.com/v1/projects/" . env('FIREBASE_PROJECT_ID') . "/messages:send";

        foreach ($tokens as $token) {
    $response = Http::withToken($accessToken)
        ->post($url, [
            'message' => [
                'token' => $token,
                'webpush' => [
                    'headers' => [
                        'Urgency' => 'high'
                    ],
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'icon' => asset('images/icon-192x192.png'),
                    ],
                ],
            ],
        ]);

    \Log::info('FCM SEND RESPONSE', [
        'token' => $token,
        'response' => $response->json(),
        'status' => $response->status()
    ]);
}
    }
}