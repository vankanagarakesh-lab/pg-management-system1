<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SystemNotification extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($notification) {
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
                // Send Email Notification
                try {
                    $emailText = "Hello {$user->name},\n\nThis is a notification from Thulasi PG:\n{$notification->text}\n\nBest regards,\nThulasi PG Team";
                    Mail::raw($emailText, function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('New Thulasi PG Notification');
                    });
                    Log::info("[Notification Mail Sent] To: {$user->email}, Content: {$notification->text}");
                } catch (\Throwable $e) {
                    Log::error("Failed to send notification email to {$user->email}: " . $e->getMessage(), [
                        'exception' => $e->getTraceAsString()
                    ]);
                }

                // Send Mobile SMS Notification
                if ($user->phone) {
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
                                Log::info("[Notification Twilio SMS Sent] To: {$user->phone}, Content: {$notification->text}");
                            } else {
                                Log::error("[Notification Twilio SMS Failed] To: {$user->phone}. Response: " . $response->body());
                            }
                        } else {
                            Log::info("[Notification SMS Sent] To: {$user->phone}, Content: {$notification->text}");
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to send SMS to {$user->phone}: " . $e->getMessage());
                    }
                }
            }
        });
    }
}
