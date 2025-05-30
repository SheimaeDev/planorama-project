<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class User
 * @package App\Models
 *
 * Representa un usuario en el sistema de autenticación.
 * Gestiona la información básica del usuario, la autenticación y las relaciones con eventos.
 *
 * @property int $id Identificador único del usuario.
 * @property string $name Nombre del usuario.
 * @property string $email Dirección de correo electrónico del usuario.
 * @property \Illuminate\Support\Carbon|null $email_verified_at Fecha y hora de verificación del correo electrónico.
 * @property string $password Contraseña del usuario (hash).
 * @property string|null $remember_token Token para "recordar sesión".
 * @property \Illuminate\Support\Carbon|null $created_at Fecha y hora de creación del registro.
 * @property \Illuminate\Support\Carbon|null $updated_at Fecha y hora de la última actualización del registro.
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications Las notificaciones del usuario.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $createdEvents Los eventos creados por este usuario.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events Los eventos en los que este usuario participa (ya sea como creador o invitado).
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Los atributos que deberían ser ocultados para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtiene los 'casts' de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Define la relación de "uno a muchos" con el modelo Event, para los eventos que el usuario ha creado.
     * Un usuario puede crear muchos eventos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'creator_id');
    }

    /**
     * Define la relación de "muchos a muchos" con el modelo Event.
     * Un usuario puede participar en muchos eventos, y un evento puede tener muchos usuarios.
     * Utiliza la tabla pivote 'event_user' y recupera el campo 'is_creator'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('is_creator')
            ->withTimestamps();
    }
}