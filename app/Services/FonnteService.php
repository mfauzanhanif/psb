<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $apiUrl = 'https://api.fonnte.com/send';
    protected ?string $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    /**
     * Send WhatsApp message via Fonnte API
     *
     * @param string $phone Phone number (will be formatted to Indonesian format)
     * @param string $message Message content
     * @return array Response with success status and message
     */
    public function send(string $phone, string $message): array
    {
        if (empty($this->token)) {
            Log::warning('Fonnte: Token not configured');
            return ['success' => false, 'message' => 'Fonnte token not configured'];
        }

        $formattedPhone = $this->formatPhoneNumber($phone);
        if (!$formattedPhone) {
            return ['success' => false, 'message' => 'Invalid phone number'];
        }

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                        'target' => $formattedPhone,
                        'message' => $message,
                        'countryCode' => '62',
                    ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                Log::info('Fonnte: Message sent successfully', ['phone' => $formattedPhone]);
                return ['success' => true, 'message' => 'Message sent successfully'];
            }

            Log::warning('Fonnte: Failed to send message', ['phone' => $formattedPhone, 'response' => $result]);
            return ['success' => false, 'message' => $result['reason'] ?? 'Failed to send message'];

        } catch (\Exception $e) {
            Log::error('Fonnte: Exception occurred', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send message to multiple recipients
     *
     * @param array $phones Array of phone numbers
     * @param string $message Message content
     * @return array Results for each recipient
     */
    public function sendBulk(array $phones, string $message): array
    {
        $results = [];
        $sentNumbers = [];

        foreach ($phones as $phone) {
            $formattedPhone = $this->formatPhoneNumber($phone);

            // Skip if already sent to this number (avoid duplicates)
            if (!$formattedPhone || in_array($formattedPhone, $sentNumbers)) {
                continue;
            }

            $sentNumbers[] = $formattedPhone;
            $results[$phone] = $this->send($phone, $message);

            // Small delay between messages to avoid rate limiting
            usleep(500000); // 0.5 second
        }

        return $results;
    }

    /**
     * Format phone number to Indonesian format
     *
     * @param string $phone
     * @return string|null
     */
    protected function formatPhoneNumber(string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert to Indonesian format
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        // Validate length (Indonesian numbers: 10-13 digits after 62)
        $length = strlen($phone);
        if ($length < 10 || $length > 15) {
            return null;
        }

        return $phone;
    }
}
