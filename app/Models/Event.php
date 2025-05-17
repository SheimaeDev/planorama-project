<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('is_creator')->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class)->withDefault([
            'name' => 'Gris',
            'hex' => '#cccccc',
        ]);
    }
}
