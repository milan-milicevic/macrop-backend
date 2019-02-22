<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'archived'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    /*public function stories()
    {
        return $this->hasMany(Story::class, 'project_id');
    }*/

    public function people()
    {
        $roles = $this->hasMany(Role::class, 'project_id')->get();

        $people = [];

        foreach ($roles as $role) {
            $member = $role->user;
            $member['role'] = $role->role;
            $people [] = $member;
        }

        return $people;
    }
}
