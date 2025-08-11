<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if (!auth()->attempt($data)) {
            return response(['message' => 'Invalid Credentials'], 401);
        }

        $request->session()->regenerate();

        $user = auth()->user();

        return response(['user' => $user]);
    }
    public function logout(){
        auth()->logout();
    }

    public function register(Request $request){
        try{
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string',
                'faculty_id' => 'sometimes|exists:faculties,id'
            ]);

            $data['password'] = bcrypt($data['password']);

            $user = User::create($data);
            auth()->login($user);

            Schedule::create([
                'user_id' => $user->id,
                'name' => 'new'
            ]);

            $user->refresh();

            return response(['user' => $user]);
        } catch(\Exception $e){
            return response(['message' => $e->getMessage()], 500);
        }
    }
}
