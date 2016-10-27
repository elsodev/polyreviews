<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FoursquareData extends Model
{
    protected $table = 'foursquare_datas';
    protected $fillable = [
        'place_id', 'ratings', 'obj_id', 'total_check_ins', 'data'
    ];

    /**
     * A FoursquareData belongs to a Place
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function place()
    {
        return $this->belongsTo(\App\Place::class);
    }
}
