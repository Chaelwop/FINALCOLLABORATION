<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Delivers text messages to the phone numbers used for two factor
 * authentication. When no SMS provider is configured the message is
 * written to the application log instead.
 */
class SmsService
{
    /**
     * Send a text message to the given phone number.
     */
    public function send(string $phone, string $message): void
    {
        if ($this->usesTwilio()) {
            $this->sendWithTwilio($phone, $message);

            return;
        }

        Log::info('Verification code delivered through the log driver.', [
            'phone' => $phone,
            'message' => $message,
        ]);
    }

    /**
     * Determine whether messages should be delivered through Twilio.
     */
    public function usesTwilio(): bool
    {
        return config('security.sms.driver') === 'twilio' && $this->twilioIsConfigured();
    }

    /**
     * Determine whether the Twilio credentials are complete.
     */
    private function twilioIsConfigured(): bool
    {
        return filled(config('services.twilio.sid'))
            && filled(config('services.twilio.secret'))
            && filled(config('services.twilio.from'));
    }

    /**
     * Deliver the message through the Twilio messages endpoint.
     */
    private function sendWithTwilio(string $phone, string $message): void
    {
        $sid = config('services.twilio.sid');
        $baseUrl = rtrim((string) config('services.twilio.base_url'), '/');

        Http::asForm()
            ->withBasicAuth($sid, config('services.twilio.secret'))
            ->post("{$baseUrl}/Accounts/{$sid}/Messages.json", [
                'From' => config('services.twilio.from'),
                'To' => $phone,
                'Body' => $message,
            ])
            ->throw();
    }
}
