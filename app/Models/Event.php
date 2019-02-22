<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable  = [
        'name',
        'description',
        'start_time',
        'end_time',
        'type',
    ];

    public function userEvents()
    {
        return $this->hasMany(UserEvent::class, 'event_id');
    }
}
