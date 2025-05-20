<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pilot extends Model
{
    //
    protected $fillable = ['name', 'gender', 'films', 'image_url'];

    public function starships() :BelongsToMany
    {
        return $this->belongsToMany(Starship::class);
    }
}
