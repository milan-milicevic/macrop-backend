<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use Pusher\PusherException;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $pusher;

    public function __construct(Pusher $pusher)
    {
        $this->pusher = $pusher;
    }

    public function sendEvent()
    {
        $data['message'] = 'Kazes ne radi?!';
        try {
            $this->pusher->trigger('my-channel', 'my-event', $data);
        } catch (PusherException $e) {
            echo $e->getMessage();
        }
        return response('successful', 200);
    }
}
