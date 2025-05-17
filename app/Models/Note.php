<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'title',
        'color_id',
        'user_id',
    ]; 

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class)->withDefault([
            'name' => 'Gris',
            'hex' => '#cccccc',
        ]);
    }
}
