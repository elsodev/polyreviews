<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';
    protected $fillable = [
        'name'  
    ];

    /**
     * A State has many Areas
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
