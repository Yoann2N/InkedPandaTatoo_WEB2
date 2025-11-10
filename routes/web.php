<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TemplateController;


// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return redirect('/template/homepage-1');
});

Route::get('/template/{page?}', [TemplateController::class, 'show']);


Route::resource('user', UserController::class);



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/{page?}', [TemplateController::class, 'show'])->where('page', '.*');