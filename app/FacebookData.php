<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacebookData extends Model
{
    use Votable;
    
    protected $table = 'facebook_datas';
    protected $fillable = [
        'place_id', 'ratings', 'obj_id', 'data' 
    ];

    protected $casts = [
        'data' => 'object'
    ];

    /**
     * A FacebookData belongs to a Place
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function place()
    {
        return $this->belongsTo(\App\Place::class);
    }
}
