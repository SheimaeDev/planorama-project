<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Note
 * @package App\Models
 *
 * Representa una nota de texto en el sistema.
 * Almacena el título de la nota, el color asociado y el usuario al que pertenece.
 *
 * @property int $id Identificador único de la nota.
 * @property string $title Título o contenido de la nota.
 * @property int $color_id ID del color asociado a la nota.
 * @property int $user_id ID del usuario al que pertenece la nota.
 * @property \Illuminate\Support\Carbon|null $created_at Fecha y hora de creación del registro.
 * @property \Illuminate\Support\Carbon|null $updated_at Fecha y hora de la última actualización del registro.
 *
 * @property-read \App\Models\User $user El usuario al que pertenece esta nota.
 * @property-read \App\Models\Color $color El color asociado a esta nota.
 */
class Note extends Model
{
    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'color_id',
        'user_id',
    ];

    /**
     * Define la relación inversa de "uno a muchos" con el modelo User.
     * Una nota pertenece a un usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define la relación inversa de "uno a muchos" con el modelo Color.
     * Una nota pertenece a un color, con un color predeterminado si no se especifica.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class)->withDefault([
            'name' => 'Gris',
            'hex' => '#cccccc',
        ]);
    }
}