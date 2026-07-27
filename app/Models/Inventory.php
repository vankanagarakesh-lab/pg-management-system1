<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    // Define custom table name since Laravel assumes inventories
    protected $table = 'inventory';
    protected $guarded = [];
}
