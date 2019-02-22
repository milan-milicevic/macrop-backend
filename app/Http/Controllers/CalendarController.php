<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function getMonth(Request $request, $user_id, $project_id){
        $month = Carbon::parse($request->month);
        $nextMont = Carbon::parse($request->month)->addMonth(1);
        $user = User::find($user_id);
        $events = $user->events();
        $tasks = $user->tasks($month, $nextMont);
        $resultEvents = [];
        foreach ($events as $event) {
            if($event['accepted'] == false)
                continue;

            if(Carbon::parse($event->start_time)->gte($month) && Carbon::parse($event->start_time)->lt($nextMont))
                $resultEvents [Carbon::parse($event->start_time)->format('m/d/Y')][] = $event;
            if(Carbon::parse($event->end_time)->gte($month) && Carbon::parse($event->end_time)->lt($nextMont))
                $resultEvents [Carbon::parse($event->end_time)->format('m/d/Y')][] = $event;
        }

        foreach ($tasks as $task) {
            if($task->project_id != $project_id)
                continue;
            $resultEvents [Carbon::parse($task->start_time)->format('m/d/Y')][] = $task;
            $resultEvents [Carbon::parse($task->end_time)->format('m/d/Y')][] = $task;
        }

        $result = [];
        $days = [];
        foreach ($resultEvents as $key => $resultEvent) {
            $result['date'] = $key;
            $result['events'] = $resultEvents[$key];
            $days[] = $result;
        }
        return $days;
    }
}
