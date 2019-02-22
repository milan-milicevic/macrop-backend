<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'name',
        'status',
        'archived',
        'start_date',
        'end_date',
    ];

    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'card_id');
    }
}
