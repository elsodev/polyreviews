<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Neighbourhood extends Model
{
    protected $table = 'neighbourhoods';

    protected $fillable = ['name'];

    /**
     * A neighbourhood belongs to An Area
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function area()
    {
        return $this->belongsTo(\App\Area::class);
    }
}
