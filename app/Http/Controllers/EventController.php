<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\UserEvent;
use http\Env\Response;
use Illuminate\Http\Request;
use Pusher\Pusher;
use Pusher\PusherException;

class EventController extends Controller
{
    protected $pusher;

    public function __construct(Pusher $pusher)
    {
        $this->pusher = $pusher;
    }

    public function store(Request $request, $user)
    {
        $this->validate($request, [
            'name' => 'required|max:191',
            'description' => 'max:191',
            'start_time' => 'required|date_format:m/d/Y H:i:s',
            'end_time' => 'date_format:m/d/Y H:i:s',
            'type' => 'required|string',
        ]);

        $user = User::find($user);

        if(!$user)
            return response()->json(['error' => 'User with that id does not exists.'], 400);

        $event = new Event;
        $event->name = $request->name;
        $event->type = $request->type;
        $event->start_time = $request->start_time;
        if($request->exists('description'))
            $event->description = $request->description;
        if($request->exists('end_time'))
            $event->end_time = $request->end_time;


        $event->save();

        $userEvent = new UserEvent;
        $userEvent->accepted = true;
        $userEvent->event()->associate($event);
        $userEvent->user()->associate($user);

        $userEvent->save();

        $error = 'No error.';
        //$data['message'] = 'Kazes ne radi?!';
        try {
            $this->pusher->trigger('macrop-channel', 'event-created', $event);
        } catch (PusherException $e) {
            echo $e->getMessage();
            $error = $e->getMessage();
        }
        $event['error'] = $error;

        return $event;
    }

    public function update(Request $request, $event)
    {
        $this->validate($request, [
            'name' => 'max:191',
            'description' => 'max:191',
            'start_time' => 'date_format:m/d/Y H:i:s',
            'end_time' => 'date_format:m/d/Y H:i:s',
            'type' => 'string',
        ]);

        $event = Event::find($event);

        if(!$event)
            return response()->json(['error' => 'Event with that id does not exists.'], 400);

        if($request->exists('name'))
            $event->name = $request->name;
        if($request->exists('type'))
            $event->type = $request->type;
        if($request->exists('start_time'))
            $event->start_time = $request->start_time;
        if($request->exists('description'))
            $event->description = $request->description;
        if($request->exists('end_time'))
            $event->end_time = $request->end_time;

        $event->update();

        return $event;
    }

    public function addUsersToEvent(Request $request, $event)
    {
        $this->validate($request, [
            'members' => 'required'
        ]);

        $event = Event::find($event);

        if(!$event)
            return response()->json(['error' => 'Event with that id does not exists.'], 400);

        foreach ($request->members as $member) {

            $user = User::find($member);

            if(!$user)
                continue;

            $userEvent = UserEvent::where(['event_id' => $event->id, 'user_id', $user->id])->first();

            if($userEvent)
                continue;

            $userEvent = new UserEvent;
            $userEvent->event()->associate($event);
            $userEvent->user()->associate($user);

            $userEvent->save();
        }

        return response()->json(['successful'], 200);
    }

    public function getUserEvents($user)
    {
        $user = User::find($user);

        if(!$user)
            return response()->json(['error' => 'User with that id does not exists.'], 400);

        $events = $user->events();

        return $events;
    }

    public function getEventUsers($event)
    {
        $event = Event::find($event);

        if(!$event)
            return response()->json(['error' => 'Event with that id does not exists.'], 400);

        $userEvents = UserEvent::where('event_id', $event->id)->get();

        $users = [];

        foreach ($userEvents as $userEvent) {
            $user = $userEvent->user;
            $users [] = $user;
        }

        return $users;
    }

    public function delete($event)
    {
        $event = Event::find($event);

        if(!$event)
            return response()->json(['error' => 'Event with that id does not exists.'], 400);

        $error = 'No error.';

        //$data['message'] = 'Kazes ne radi?!';
        try {
            $this->pusher->trigger('macrop-channel', 'event-deleted', $event);
        } catch (PusherException $e) {
            echo $e->getMessage();
            $error = $e->getMessage();
        }

        $event->delete();

        return response()->json([
            'message' => 'You have successfully deleted an event.',
            'error' => $error], 400);
    }
}
