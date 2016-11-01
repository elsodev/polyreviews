<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacebookData extends Model
{
    protected $table = 'facebook_datas';
    protected $fillable = [
        'place_id', 'ratings', 'obj_id', 'data' 
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

    /**
     * A FacebookData has many Votes(polymorphic)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function votes()
    {
        return $this->morphMany(\App\Vote::class, 'obj');
    }

}
