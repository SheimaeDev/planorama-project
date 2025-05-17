<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
