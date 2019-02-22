<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name',
        'status',
        'archived',
        'start_date',
        'end_date',
    ];

    /*public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }*/

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function files()
    {
        return $this->hasMany(TaskFile::class, 'task_id');
    }

    public function users()
    {
        $taskUsers = $this->hasMany(UserTask::class, 'task_id')->get();

        $users = [];

        foreach ($taskUsers as $taskUser) {
            $users [] = $taskUser->user;
        }

        return $users;
    }
}
