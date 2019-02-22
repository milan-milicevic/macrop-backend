<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Project;
use App\Models\Story;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTask;
use Illuminate\Http\Request;
use Pusher\Pusher;

class TaskController extends Controller
{
    protected $pusher;

    public function __construct(Pusher $pusher)
    {
        $this->pusher = $pusher;
    }

    public function index($project)
    {
        $project = Card::find($project);

        if(!$project)
            return response()->json(['error' => 'Project with that id does not exists.'], 400);

        $task = Task::where('project_id', $project->id)->get();

        return $task;
    }
    public function get($task)
    {
        $task = Task::find($task);

        if(!$task)
            return response()->json(['error' => 'Task with that id does not exists.'], 400);

        $files = $task->files;
        $task['files'] = $files;

        return $task;

    }

    public function store(Request $request, $project, $user)
    {
        $project = Project::find($project);
        $user = User::find($user);

        if(!$project)
            return response()->json(['error' => 'Project with that id does not exists.'], 400);

        if(!$user)
            return response()->json(['error' => 'User with that id does not exists.'], 400);

        $this->validate($request, [
            'name' => 'required|max:40',
            'start_date' => 'required|date_format:m/d/Y H:i:s',
            'end_date' => 'required|date_format:m/d/Y H:i:s'
        ]);

        $task = new Task;
        $task->name = $request->name;
        $task->status = 'Active';
        $task->start_date = $request->start_date;
        $task->end_date = $request->end_date;
        $task->archived = false;

        $task->project()->associate($project);

        $task->save();

        $userTask = new UserTask;
        $userTask->user()->associate($user);
        $userTask->task()->associate($task);
        $userTask->save();

        $people = $project->people();
        foreach ($people as $person) {
            $userTask = new UserTask;
            $userTask->task()->associate($task);
            $userTask->user()->associate($person);
            $userTask->save();
        }

        $error = 'No error.';
        //$data['message'] = 'Kazes ne radi?!';
        try {
            $this->pusher->trigger('macrop-channel', 'event-created', $task);
        } catch (PusherException $e) {
            echo $e->getMessage();
            $error = $e->getMessage();
        }
        $task['error'] = $error;

        return $task;
    }

    public function update(Request $request, $task)
    {
        $this->validate($request, [
            'name' => 'max:40',
            'start_date' => 'date_format:m/d/Y H:i:s',
            'end_date' => 'date_format:m/d/Y H:i:s',
            'archived' => 'boolean',
            'status' => 'max:40'
        ]);

        $task = Task::find($task);

        if(!$task)
            return response()->json(['error' => 'Task with that id does not exists.'], 400);

        if($request->exists('name'))
            $task->name = $request->name;
        if($request->exists('start_date'))
            $task->start_date = $request->start_date;
        if($request->exists('end_date'))
            $task->end_date = $request->end_date;
        if($request->exists('archived'))
            $task->archived = $request->archived;
        if($request->exists('status'))
            $task->status = $request->status;

        $task->update();

        return $task;
    }

    public function delete($task)
    {
        $task = Task::find($task);

        if(!$task)
            return response()->json(['error' => 'Task with that id does not exists.'], 400);

        try {
            $this->pusher->trigger('macrop-channel', 'event-created', $task);
        } catch (PusherException $e) {
            echo $e->getMessage();
            $error = $e->getMessage();
        }
        $task['error'] = $error;

        $task->delete();

        return $task;
    }

    /*public function getFiles($task)
    {
        $task = Task::find($task);

        if(!$task)
            return response()->json(['error' => 'Task with that id does not exists.'], 400);

        $files = $task->files;

        return $files;
    }*/

    public function uploadFile(Request $request, $task)
    {
        $task = Task::find($task);

        if(!$task)
            return response()->json(['error' => 'Task with that id does not exists.'], 400);

        $this->validate($request, [
            'name' => 'required|max:100',
            'file' => 'required'
        ]);

        $file_content = file_get_contents($request->file);
        $path = "/files/" . $task->id . "/" . $request->name;

        //$file_url = url($path);

        //Storage::disk('s3')->put($path, $file_content, 'public');
        //$ur = 'https://s3.eu-central-1.amazonaws.com/cransten-images' . $path;
        //return $ur;

        //return response()->json($file_url, 200);
    }

    public function assignTask($task, $user)
    {
        $task = Task::find($task);

        if(!$task)
            return response()->json(['error' => 'Task with that id does not exists.'], 400);

        $user = User::find($user);

        if(!$user)
            return response()->json(['error' => 'User with that id does not exists.'], 400);

        $userTask = UserTask::where(['task_id' => $task->id, 'user_id' => $user->id])->first();

        if($userTask)
            return response()->json(['error' => 'User is already assigned to this task.'], 400);

        $userTask = new UserTask;
        $userTask->user_id = $user->id;
        $userTask->task_id = $task->id;

        $userTask->save();

        return $userTask;
    }

    public function getTaskUsers($task)
    {
        $task = Task::find($task);

        if(!$task)
            return response()->json(['error' => 'Task with that id does not exists.'], 400);

        $users = $task->users();

        return $users;
    }
}
