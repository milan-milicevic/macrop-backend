<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\UserTask;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function store(Request $request, $user)
    {
        $user = User::find($user);

        if(!$user)
            return response()->json(['error' => 'User with that id does not exists.'], 400);

        $this->validate($request, [
            'name' => 'required|max:40',
            'description' => 'required|max:150',
        ]);

        $project = new Project;
        $project->name = $request->name;
        $project->description = $request->description;

        $project->owner()->associate($user);

        $project->save();

        return $project;
    }

    public function index($user)
    {
        $user = User::find($user);

        if(!$user)
            return response()->json(['error' => 'User with that id does not exists.'], 400);

        //$projects = Project::where('user_id', $user->id)->get();
        $ownedProjects = $user->project;
        $belongsTo = $user->isOnProjects();

        $merged = $ownedProjects->merge($belongsTo);

        foreach ($merged as $item){
            $people = $item->people();
            $item['people'] = $people;
            unset($item['owner']);
        }

        return $merged;
    }

    public function get($project)
    {
        $project = Project::find($project);

        if(!$project)
            return response()->json(['error' => 'Project with that id does not exists.'], 400);

        $people = $project->people();
        $project['people'] = $people;

        return $project;
    }

    public function update(Request $request, $project)
    {
        $this->validate($request, [
            'name' => 'max:40',
            'description' => 'max:150',
            'archived' => 'boolean',
        ]);

        $project = Project::find($project);

        if(!$project)
            return response()->json(['error' => 'Project with that id does not exists.'], 400);

        if($request->exists('name'))
            $project->name = $request->name;
        if($request->exists('description'))
            $project->description = $request->description;
        if($request->exists('archived'))
            $project->archived = $request->archived;

        $project->update();

        return $project;
    }

    public function delete($project)
    {
        $project = Project::find($project);

        if(!$project)
            return response()->json(['error' => 'Project with that id does not exists.'], 400);

        $project->delete();

        return response()->json(['message' => 'Project has been deleted.'], 200);
    }

    public function addMember(Request $request, $project)
    {
        $project = Project::find($project);

        if(!$project)
            return response()->json(['error' => 'Project with that id does not exists.'], 400);

        $this->validate($request, [
            'role' => 'required|max:40',
            'user_id' => 'required|exists:users,id',
        ]);

        $role = Role::where(['project_id' => $project->id, 'user_id' => $request->user_id])->first();

        if($role)
            return response()->json(['error' => 'User is already on this project.'], 400);

        $role = new Role;
        $role->role = $request->role;
        $role->user_id = $request->user_id;
        $role->project_id = $project->id;

        $role->save();

        $tasks = $project->tasks;
        $people = $project->people();
        foreach ($people as $person) {
            foreach ($tasks as $task) {
                $userTask = new UserTask;
                $userTask->task()->associate($task);
                $userTask->user()->associate($person);
                $userTask->save();
            }
        }

        return $role;
    }
}
