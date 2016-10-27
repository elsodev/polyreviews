<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $table = 'places';
    protected $fillable = [
        'lng', 'lat', 'name', 'address', 'description', 'contact', 'avg_rating', 'last_fetch'
    ];

    /**
     * A Place belongs to many Categories
     * (Example: A restaurant can be a : Japanese, Malay, Chinese restaurant category as it serves a mix of the food
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(\App\Category::class, 'places_categories', 'place_id');
    }
}
