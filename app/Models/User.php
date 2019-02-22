<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Laravel\Passport\HasApiTokens;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'gender'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function project()
    {
        return $this->hasMany(Project::class, 'user_id')->with('owner');
    }

    public function isOnProjects()
    {
        $roles = $this->hasMany(Role::class, 'user_id')->get();

        $projects = [];

        foreach ($roles as $role) {
            $project = $role->project;
            $project['owner'] = $project->owner;
            $project['role'] = $role->role;
            $projects [] = $project;
        }

        return $projects;
    }

    public function events()
    {
        $userEvents = $this->hasMany(UserEvent::class, 'user_id')->get();

        $events = [];

        foreach ($userEvents as $userEvent)
        {
            $event = $userEvent->event;
            $event['accepted'] = $userEvent->accepted;
            $events [] = $event;
        }

        return $events;
    }

    public function tasks($month = null, $nextMonth = null)
    {
        $userTasks = $this->hasMany(UserTask::class, 'user_id')->get();

        $tasks = [];

        foreach ($userTasks as $userTask)
        {
            $task = $userTask->task;
            if($month != null){
                if(Carbon::parse($task->start_date)->lt($month) || Carbon::parse($task->end_date)->gte($nextMonth))
                    continue;
            }

            if($task->archived)
                continue;
            $task['start_time'] = $task->start_date;
            $task['end_time'] = $task->end_date;
            $task['type'] = 'task';
            $tasks [] = $task;
        }

        return $tasks;
    }
}
