<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user){
        return response()->json($user);
    }

    public function delete(User $user){
        if(auth()->user()->id !== $user->id)
            return response("Action prohibited", 403);

        $user->delete();
        return response("User deleted", 200);
    }

    public function store(Request $request, User $user){
        if(auth()->user()->id !== $user->id)
            return response("Action prohibited", 403);

        $user->fill($request->all());
        $user->save();
        return response()->json($user);
    }
}
