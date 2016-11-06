<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name' , 'foursquare_id'
    ];
    
    public function places()
    {
        return $this->belongsToMany(\App\Place::class, 'places_categories', 'category_id');
    }
}
