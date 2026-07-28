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
            // Dispatch notification email sending asynchronously to prevent request blocking / latency
            \App\Jobs\SendNotificationEmailJob::dispatch($notification->id);
        });
    }
}
