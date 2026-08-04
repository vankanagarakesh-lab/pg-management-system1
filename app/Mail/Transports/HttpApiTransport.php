<?php

namespace App\Mail\Transports;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpApiTransport extends AbstractTransport
{
    protected string $apiKey;
    protected string $provider;

    public function __construct(string $apiKey, string $provider = 'auto')
    {
        parent::__construct();
        $this->apiKey = trim($apiKey);
        $this->provider = $provider;
    }

    protected function doSend(SentMessage $message): void
    {
        $apiKey = trim($this->apiKey, " \t\n\r\0\x0B\"'");
        if (empty($apiKey) || (!str_contains($apiKey, '-') && !str_starts_with($apiKey, 're_'))) {
            $apiKey = trim((string)(env('MAIL_PASSWORD') ?: (env('MAIL_MAILER') ?: (env('BREVO_API_KEY') ?: ''))), " \t\n\r\0\x0B\"'");
        }

        $email = $message->getOriginalMessage();

        $to = [];
        foreach ($email->getTo() as $address) {
            $to[] = [
                'email' => $address->getAddress(),
                'name' => $address->getName() ?: $address->getAddress(),
            ];
        }

        $fromList = $email->getFrom();
        $from = $fromList[0] ?? null;
        $rawFromEmail = $from ? $from->getAddress() : null;

        if (!empty($rawFromEmail) && filter_var($rawFromEmail, FILTER_VALIDATE_EMAIL) && !str_contains($rawFromEmail, 'example.com')) {
            $fromEmail = $rawFromEmail;
        } else {
            $fromEmail = env('MAIL_FROM_ADDRESS') ?: (env('MAIL_USERNAME') ?: 'vankarajesh41@gmail.com');
        }

        $fromName = ($from && !empty($from->getName())) ? $from->getName() : (env('MAIL_FROM_NAME') ?: config('mail.from.name', 'Thulasi PG'));

        $subject = $email->getSubject() ?: 'Thulasi PG Notification';
        $textBody = $email->getTextBody() ?: strip_tags($email->getHtmlBody() ?? '');
        $htmlBody = $email->getHtmlBody() ?: nl2br(e($textBody));

        // Auto-detect provider
        $provider = $this->provider;
        if ($provider === 'auto') {
            if (str_starts_with($apiKey, 're_')) {
                $provider = 'resend';
            } else {
                $provider = 'brevo';
            }
        }

        if ($provider === 'resend' || str_starts_with($apiKey, 're_')) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.resend.com/emails', [
                'from' => "{$fromName} <{$fromEmail}>",
                'to' => array_column($to, 'email'),
                'subject' => $subject,
                'text' => $textBody,
                'html' => $htmlBody,
            ]);

            if ($response->failed()) {
                Log::error("[HttpApiTransport] Resend API Error ({$response->status()}): " . $response->body());
                throw new \RuntimeException("Resend HTTPS API delivery failed: " . $response->body());
            }
        } else {
            // Send via Brevo REST API v3 over HTTPS Port 443
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => $fromName,
                    'email' => $fromEmail,
                ],
                'to' => $to,
                'subject' => $subject,
                'textContent' => $textBody,
                'htmlContent' => $htmlBody,
            ]);

            if ($response->failed()) {
                Log::error("[HttpApiTransport] Brevo API Error ({$response->status()}): " . $response->body());
                throw new \RuntimeException("Brevo HTTPS API delivery failed ({$response->status()}): " . $response->body());
            }

            $resData = $response->json();
            $msgId = $resData['messageId'] ?? 'N/A';
            Log::info("[HttpApiTransport] Email delivered via Brevo API to " . implode(', ', array_column($to, 'email')) . " (Message ID: {$msgId})");
        }
    }

    public function __toString(): string
    {
        return 'http_api';
    }
}
