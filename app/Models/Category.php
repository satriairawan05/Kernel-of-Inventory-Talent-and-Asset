<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['company_id','category_name','description'];

    public function company(): BelongsTo 
    {
        return $this->belongsTo(Company::class);
    }
}
