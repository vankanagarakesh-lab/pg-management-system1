<?php

namespace App\Jobs;

use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendNotificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    protected int $notificationId;

    public function __construct(int $notificationId)
    {
        $this->notificationId = $notificationId;
    }

    public function handle(): void
    {
        $notification = SystemNotification::find($this->notificationId);
        if (!$notification) {
            Log::warning("[SendNotificationEmailJob] Notification ID {$this->notificationId} not found.");
            return;
        }

        if ($notification->user_id) {
            $users = User::where('id', $notification->user_id)->get();
        } else if ($notification->type === 'all') {
            $users = User::all();
        } else {
            $users = User::where('role', $notification->type)
                        ->orWhere('staff_role', $notification->type)
                        ->get();
        }

        foreach ($users as $user) {
            if (empty($user->email)) {
                continue;
            }

            // Send Email Notification
            try {
                $emailText = "Hello {$user->name},\n\nThis is a notification from Thulasi PG:\n{$notification->text}\n\nBest regards,\nThulasi PG Team";
                Mail::raw($emailText, function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('New Thulasi PG Notification');
                });
                Log::info("[Notification Mail Sent] To: {$user->email}, Notification ID: {$notification->id}");
            } catch (\Throwable $e) {
                Log::error("Failed to send notification email to {$user->email}: " . $e->getMessage());
            }

            // Send Mobile SMS Notification if Twilio is configured
            if (!empty($user->phone)) {
                try {
                    $sid = env('TWILIO_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_NUMBER');

                    if (!empty($sid) && !empty($token) && !empty($from)) {
                        $response = Http::withBasicAuth($sid, $token)
                            ->asForm()
                            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                                'To' => $user->phone,
                                'From' => $from,
                                'Body' => $notification->text,
                            ]);
                        
                        if ($response->successful()) {
                            Log::info("[Notification Twilio SMS Sent] To: {$user->phone}");
                        } else {
                            Log::error("[Notification Twilio SMS Failed] To: {$user->phone}. Response: " . $response->body());
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error("Failed to send SMS to {$user->phone}: " . $e->getMessage());
                }
            }
        }
    }
}
