<?php

namespace App;

trait Votable {

    /**
     * A VotableClass has many Votes(polymorphic)
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function votes()
    {
        return $this->morphMany(\App\Vote::class, 'obj');
    }


    public function upVotesCount()
    {
        return $this->morphOne(\App\Vote::class, 'obj')
            ->where('vote_type', 1)
            ->selectRaw('obj_id, count(*) as aggregate')
            ->groupBy('obj_id');
    }

    public function downVotesCount()
    {
        return $this->morphOne(\App\Vote::class, 'obj')
            ->where('vote_type', 0)
            ->selectRaw('obj_id, count(*) as aggregate')
            ->groupBy('obj_id');
    }


    // source: https://softonsofa.com/tweaking-eloquent-relations-how-to-get-hasmany-relation-count-efficiently/
    public function getUpVotesCountAttribute()
    {
        // if relation is not loaded already, let's do it first
        if ( ! array_key_exists('upVotesCount', $this->relations))
            $this->load('upVotesCount');

        $related = $this->getRelation('upVotesCount');

        // then return the count directly
        return ($related) ? (int) $related->aggregate : 0;
    }


    // source: https://softonsofa.com/tweaking-eloquent-relations-how-to-get-hasmany-relation-count-efficiently/
    public function getDownVotesCountAttribute()
    {
        // if relation is not loaded already, let's do it first
        if ( ! array_key_exists('downVotesCount', $this->relations))
            $this->load('downVotesCount');

        $related = $this->getRelation('downVotesCount');

        // then return the count directly
        return ($related) ? (int) $related->aggregate : 0;
    }


}