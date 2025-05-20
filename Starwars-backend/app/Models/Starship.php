<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Starship extends Model
{

    protected $fillable = ['name', 'model', 'manufacturer', 'cost_in_credits', 'image2_url', 'user_id'];


    public function pilots(): BelongsToMany
    {
        return $this->belongsToMany(Pilot::class);
    }

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
