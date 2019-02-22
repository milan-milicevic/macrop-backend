<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $fillable = [
        'name'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'story_id');
    }
}
