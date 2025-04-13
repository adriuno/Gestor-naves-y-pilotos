<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pilot extends Model
{
    //
    protected $fillable = ['name', 'gender', 'films', 'image_url'];

    public function starships()
    {
        return $this->belongsToMany(Starship::class);
    }
}
