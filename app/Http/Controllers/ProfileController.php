<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Class ProfileController
 * @package App\Http\Controllers
 *
 * Controlador responsable de gestionar las operaciones relacionadas con el perfil de usuario.
 * Esto incluye mostrar el formulario de perfil, actualizar la información del usuario y eliminar la cuenta.
 */
class ProfileController extends Controller
{
    /**
     * Muestra el formulario de perfil del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request  La instancia de la petición HTTP.
     * @return \Illuminate\View\View Retorna la vista 'profile.edit' con los datos del usuario.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Actualiza la información de perfil del usuario autenticado.
     *
     * Valida los datos de entrada a través de `ProfileUpdateRequest`.
     * Si el correo electrónico cambia, se anula la verificación del email.
     *
     * @param  \App\Http\Requests\ProfileUpdateRequest  $request  La solicitud que contiene los datos validados del perfil.
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'profile.edit' con un mensaje de estado 'profile-updated'.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Elimina la cuenta del usuario autenticado.
     *
     * Requiere que el usuario confirme su contraseña actual antes de proceder.
     * Después de la eliminación, se cierra la sesión del usuario, se invalida la sesión
     * y se regenera el token CSRF, para luego redirigir a la página de inicio.
     *
     * @param  \Illuminate\Http\Request  $request  La instancia de la petición HTTP, que debe contener la contraseña para validación.
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta raíz ('/').
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}