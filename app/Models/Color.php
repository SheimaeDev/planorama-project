<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Color
 * @package App\Models
 *
 * Representa un color que puede ser asociado a eventos o notas.
 * Almacena el nombre del color y su código hexadecimal.
 *
 * @property int $id Identificador único del color.
 * @property string $name Nombre del color (ej. "Rojo", "Azul").
 * @property string $hex_code Código hexadecimal del color (ej. "#FF0000").
 * @property \Illuminate\Support\Carbon $created_at Fecha y hora de creación del registro.
 * @property \Illuminate\Support\Carbon $updated_at Fecha y hora de la última actualización del registro.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events Los eventos asociados a este color.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes Las notas asociadas a este color.
 */
class Color extends Model
{
    /**
     * Define la relación de "uno a muchos" con el modelo Event.
     * Un color puede tener múltiples eventos asociados.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Define la relación de "uno a muchos" con el modelo Note.
     * Un color puede tener múltiples notas asociadas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}