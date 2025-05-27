<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'creator_id',
        'color_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_creator')
            ->withTimestamps();
    }

    public function sharedUsers()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_creator')
            ->withTimestamps()
            ->wherePivot('is_creator', false);
    }

    public function creatorUser()
    {
        return $this->users()
            ->wherePivot('is_creator', true)
            ->first();
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
