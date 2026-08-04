<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {email : The recipient email address}';
    protected $description = 'Send a test email to verify SMTP mail delivery configuration on Render/local.';

    public function handle()
    {
        $recipient = $this->argument('email');
        $this->info("Attempting to send test email to: {$recipient}...");

        try {
            $fromAddress = config('mail.from.address') ?: (env('MAIL_USERNAME') ?: 'hello@example.com');
            $fromName = config('mail.from.name') ?: (env('APP_NAME') ?: 'Thulasi PG');
            Mail::raw("Hello,\n\nThis is a test notification email sent from Thulasi PG Management System to verify Render SMTP setup.\n\nTime: " . date('Y-m-d H:i:s'), function ($message) use ($recipient, $fromAddress, $fromName) {
                $message->from($fromAddress, $fromName)
                        ->to($recipient)
                        ->subject('Thulasi PG - Render Test Notification');
            });

            $this->info("SUCCESS: Test email sent successfully to {$recipient}!");
            return 0;
        } catch (\Throwable $e) {
            $this->error("ERROR: Failed to send test email.");
            $this->error("Message: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
