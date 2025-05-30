<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Event
 * @package App\Models
 *
 * Representa un evento en el sistema.
 * Almacena información sobre el título, fechas, creador y color del evento,
 * así como la relación con los usuarios que participan en él.
 *
 * @property int $id Identificador único del evento.
 * @property string $title Título del evento.
 * @property \Illuminate\Support\Carbon $start_date Fecha y hora de inicio del evento.
 * @property \Illuminate\Support\Carbon $end_date Fecha y hora de fin del evento.
 * @property int $creator_id ID del usuario que creó el evento.
 * @property int|null $color_id ID del color asociado al evento (opcional).
 * @property \Illuminate\Support\Carbon|null $created_at Fecha y hora de creación del registro.
 * @property \Illuminate\Support\Carbon|null $updated_at Fecha y hora de la última actualización del registro.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users Los usuarios asociados a este evento.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $sharedUsers Usuarios que han sido compartidos en este evento (no son el creador).
 * @property-read \App\Models\User|null $creatorUser El usuario creador de este evento.
 * @property-read \App\Models\User $creator El usuario que creó este evento.
 * @property-read \App\Models\Color $color El color asociado a este evento.
 */
class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'creator_id',
        'color_id',
    ];

    /**
     * Define la relación de "muchos a muchos" con el modelo User.
     * Un evento puede tener múltiples usuarios asociados, y un usuario puede participar en múltiples eventos.
     * Utiliza la tabla pivote 'event_user' para almacenar información adicional sobre la relación.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_creator')
            ->withTimestamps();
    }

    /**
     * Define la relación de "muchos a muchos" con el modelo User, filtrando solo los usuarios que no son creadores.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sharedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_creator')
            ->withTimestamps()
            ->wherePivot('is_creator', false);
    }

    /**
     * Obtiene el usuario creador del evento.
     *
     * @return \App\Models\User|null
     */
    public function creatorUser(): ?User
    {
        return $this->users()
            ->wherePivot('is_creator', true)
            ->first();
    }

    /**
     * Define la relación de "uno a muchos inversa" con el modelo User (creador).
     * Un evento pertenece a un usuario creador.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Define la relación de "uno a muchos inversa" con el modelo Color.
     * Un evento pertenece a un color.
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