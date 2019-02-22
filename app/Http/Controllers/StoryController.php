<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index($project)
    {
        $project = Project::find($project);

        if(!$project)
            return response()->json(['error' => 'Project with that id does not exists.'], 400);

        $stories = Story::where('project_id', $project->id)->get();

        return $stories;
    }

    public function get($story)
    {
        $story = Story::find($story);

        if(!$story)
            return response()->json(['error' => 'Story with that id does not exists.'], 400);

        return $story;

    }

    public function store(Request $request, $project)
    {
        $project = Project::find($project);

        if(!$project)
            return response()->json(['error' => 'Project with that id does not exists.'], 400);

        $this->validate($request, [
            'name' => 'required|max:40',
        ]);

        $story = new Story;
        $story->name = $request->name;

        $story->project()->associate($project);

        $story->save();

        return $story;
    }

    public function update(Request $request, $story)
    {
        $this->validate($request, [
            'name' => 'max:40',
        ]);

        $story = Story::find($story);

        if(!$story)
            return response()->json(['error' => 'Story with that id does not exists.'], 400);

        if($request->exists('name'))
            $story->name = $request->name;

        $story->update();

        return $story;
    }

    public function delete($story)
    {
        $story = Story::find($story);

        if(!$story)
            return response()->json(['error' => 'Story with that id does not exists.'], 400);

        return $story;
    }
}
