<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    // Define the table name used by this model
    protected $table = 'units';

    // Allow mass assignment for these fields
    protected $fillable = [
        'unit_name','unit_code','description','is_active'
    ];
}
