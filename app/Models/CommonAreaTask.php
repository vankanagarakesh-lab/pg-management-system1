<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonAreaTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'pg_building_id',
        'area_name',
        'status',
        'last_cleaned_at'
    ];

    public function building()
    {
        return $this->belongsTo(PgBuilding::class, 'pg_building_id');
    }
}
