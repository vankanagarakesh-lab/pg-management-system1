<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'staff_role',
        'report_text',
        'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
