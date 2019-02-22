<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Story;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index($story)
    {
        $story = Story::find($story);

        if(!$story)
            return response()->json(['error' => 'Story with that id does not exists.'], 400);

        $cards = Card::where('story_id', $story->id)->get();

        return $cards;
    }
    public function get($card)
    {
        $card = Card::find($card);

        if(!$card)
            return response()->json(['error' => 'Card with that id does not exists.'], 400);

        return $card;

    }

    public function store(Request $request, $story)
    {
        $story = Story::find($story);

        if(!$story)
            return response()->json(['error' => 'Story with that id does not exists.'], 400);

        $this->validate($request, [
            'name' => 'required|max:40',
            'start_date' => 'required|date_format:m/d/Y H:i:s' ,
            'end_date' => 'required|date_format:m/d/Y H:i:s'
        ]);

        $card = new Card;
        $card->name = $request->name;
        $card->status = 'Active';
        $card->start_date = $request->start_date;
        $card->end_date = $request->end_date;
        $card->archived = false;

        $card->story()->associate($story);

        $card->save();

        return $card;
    }

    public function update(Request $request, $card)
    {
        $this->validate($request, [
            'name' => 'max:40',
            'start_date' => 'date_format:m/d/Y H:i:s',
            'end_date' => 'date_format:m/d/Y H:i:s',
            'archived' => 'boolean',
            'status' => 'max:40'
        ]);

        $card = Card::find($card);

        if($request->exists('name'))
            $card->name = $request->name;
        if($request->exists('start_date'))
            $card->start_date = $request->start_date;
        if($request->exists('end_date'))
            $card->end_date = $request->end_date;
        if($request->exists('archived'))
            $card->archived = $request->archived;
        if($request->exists('status'))
            $card->status = $request->status;

        $card->update();

        return $card;
    }

    public function delete($card)
    {
        $card = Card::find($card);

        if(!$card)
            return response()->json(['error' => 'Card with that id does not exists.'], 400);

        return $card;
    }
}
