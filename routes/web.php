<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Auth;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('events.index');
    }

    return view('welcome');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('events', EventController::class);

    Route::resource('notes', NoteController::class);

    Route::get('/news', [NewsController::class, 'index'])->name('news.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/events/day/{date?}', [EventController::class, 'showDayView'])->name('events.day');

});

require __DIR__.'/auth.php';
