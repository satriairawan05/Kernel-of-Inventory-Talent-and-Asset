<?php

namespace App\Models;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'group_pages', 'id', 'page_id')
                    ->withPivot('access')
                    ->withTimestamps();
    }
}
