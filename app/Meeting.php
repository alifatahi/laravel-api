<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Meeting
 * @package App
 */
class Meeting extends Model
{

    /**
     * @var array
     */
    protected $fillable = ['time', 'title', 'description'];

    /**
     * Each Can Register For Many Meeting
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
