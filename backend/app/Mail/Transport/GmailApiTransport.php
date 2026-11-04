<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\MessageConverter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GmailApiTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        
        $token = $this->getAccessToken();
        if (!$token) {
            throw new \Exception('Gmail API OAuth is not connected. Please authorize Gmail in the admin panel.');
        }

        // Convert the mail message to RFC 2822 raw string
        $rawMessage = $email->toString();
        
        // Base64Url encode
        $safeRaw = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($rawMessage));

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', [
            'raw' => $safeRaw
        ]);

        if ($response->failed()) {
            Log::error('Gmail API Send Failed:', ['body' => $response->body()]);
            throw new \Exception('Failed to send email via Gmail API: ' . $response->body());
        }
    }

    protected function getAccessToken(): ?string
    {
        $tokenFile = storage_path('app/gmail_token.json');
        $secretFile = storage_path('app/gmail_client_secret.json');

        if (!file_exists($tokenFile) || !file_exists($secretFile)) {
            return null;
        }

        $tokenData = json_decode(file_get_contents($tokenFile), true);
        $secretData = json_decode(file_get_contents($secretFile), true)['web'] ?? [];

        if (empty($tokenData) || empty($secretData)) {
            return null;
        }

        // Check if token is expired (expiry is usually 3600 seconds)
        // We track 'expires_at' (timestamp) when storing the token
        $expiresAt = $tokenData['expires_at'] ?? 0;
        
        // If expired, refresh the token
        if ($expiresAt <= time() + 30) { // Refresh 30 seconds before actual expiration
            $response = Http::withoutVerifying()->post('https://oauth2.googleapis.com/token', [
                'client_id' => $secretData['client_id'],
                'client_secret' => $secretData['client_secret'],
                'refresh_token' => $tokenData['refresh_token'],
                'grant_type' => 'refresh_token',
            ]);

            if ($response->failed()) {
                Log::error('Gmail API Token Refresh Failed:', ['body' => $response->body()]);
                return null;
            }

            $newTokenData = $response->json();
            $tokenData['access_token'] = $newTokenData['access_token'];
            $tokenData['expires_at'] = time() + ($newTokenData['expires_in'] ?? 3600);
            
            // Keep original refresh_token if new one isn't returned
            if (isset($newTokenData['refresh_token'])) {
                $tokenData['refresh_token'] = $newTokenData['refresh_token'];
            }

            file_put_contents($tokenFile, json_encode($tokenData));
        }

        return $tokenData['access_token'];
    }

    public function __toString(): string
    {
        return 'gmail_api';
    }
}
