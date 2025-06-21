<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'name',
    ];

    public function users(){
        return $this->hasMany(User::class);
    }

    public function events(){
        return $this->hasMany(Event::class);
    }
}
