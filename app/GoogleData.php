<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleData extends Model
{
    protected $table = 'google_datas';
    protected $fillable = [
        'place_id', 'title', 'description', 'link', 'relevantOrder', 'data'
    ];

    /**
     * A GoogleData belongs to a Place
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function place()
    {
        return $this->belongsTo(\App\Place::class);
    }
    
}
