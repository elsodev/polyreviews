<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $table = 'places';
    protected $fillable = [
        'lng', 'lat', 'name', 'address', 'description', 'contact', 'avg_rating', 'last_fetch'
    ];
    
    public function categories()
    {
        return $this->belongsToMany(\App\Category::class, 'places_categories', 'place_id');
    }
}
