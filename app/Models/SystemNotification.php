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
            try {
                // Force immediate synchronous notification email delivery
                \App\Jobs\SendNotificationEmailJob::dispatchSync($notification->id);
            } catch (\Throwable $e) {
                Log::error("[SystemNotification] Failed to dispatch notification email job: " . $e->getMessage());
            }
        });
    }
}
