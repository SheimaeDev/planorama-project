<?php

//Controladores de Brazze
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

//Mis controladores 
use App\Http\Controllers\EventController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NewsController;

//Rutas gestionadas por laravel Brazze

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

//Redireccion al calendario

Route::get('/', function () {
    return redirect()->route('events.index');
});

// //Rutas gestionadas por mi

Route::middleware(['auth'])->group(function () {
    Route::resource('events', EventController::class);
    Route::resource('notes', NoteController::class);
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
});