<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function all(){
        $faculties = Faculty::all();
        return response()->json(compact('faculties'));
    }
}
