<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $table = 'votes';
    protected $fillable = [
        'user_id', 'obj_id', 'obj_type', 'vote_type'
    ];

    /**
     * A Vote belongs to A User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    
    public function obj()
    {
        return $this->morphTo();
    }

}
