<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|unique:users|max:100|email',
            'password' => 'required||max:191',
            'gender' => 'max:1',
        ]);

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        if($request->exists('gender'))
            $user->gender = $request->gender;

        $user->save();

        return $user;
    }

    public function get($user)
    {
        $user = User::find($user);

        if(!$user)
            return response()->json(['error' => 'User with that id does not exists.'], 400);

        return $user;
    }

    public function getUser(Request $request)
    {
        return $request->user();
    }

    public function update($user, Request $request)
    {
        $this->validate($request, [
            'first_name' => 'max:100',
            'last_name' => 'max:100',
            'gender' => 'max:1',
        ]);

        $user = User::find($user);

        if(!$user)
            return response()->json(['error' => 'User with that id does not exists.'], 400);

        if($request->exists('first_name'))
            $user->first_name = $request->first_name;
        if($request->exists('last_name'))
            $user->last_name = $request->last_name;
        if($request->exists('gender'))
            $user->gender = $request->gender;

        $user->update();

        return $user;
    }

    public function delete($user)
    {
        $user = User::find($user);

        if(!$user)
            return response()->json(['error' => 'User with that id does not exists.'], 400);

        $user->delete();

        return response()->json(['message' => 'User has been deleted.'], 200);
    }

    public function filter(Request $request)
    {
        $queryString = $request->keys();
        $pageSize = $request->page_size;

        unset($queryString[array_search('page_size',$queryString)]);

        $searchString = [];
        foreach ($queryString as $query) {
            $searchString[$query] = $request->{$query};
        }

        $users = User::where($searchString)->take($pageSize)->get();
        return $users;

        return $queryString;
    }
}
