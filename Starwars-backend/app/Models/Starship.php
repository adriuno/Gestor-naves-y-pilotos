<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Starship extends Model
{

    protected $fillable = ['name', 'model', 'manufacturer', 'cost_in_credits', 'image2_url'];


    public function pilots()
    {
        return $this->belongsToMany(Pilot::class);
    }
}
