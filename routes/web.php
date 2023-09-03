<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ルーター
Route::get('/sample',[\App\Http\Controllers\Sample\IndexController::class,'show']);
Route::get('/sample/{id}',[\App\Http\Controllers\Sample\IndexController::class,'showId']);
Route::get('/toduru',\App\Http\Controllers\Toduru\IndexController::class)->name('toduru.index');
Route::post('/toduru/create',\App\Http\Controllers\Toduru\CreateController::class)
    ->middleware('auth')
    ->name('toduru.create');
Route::get('/toduru/update/{toduruId}',\App\Http\Controllers\Toduru\Update\IndexController::class)->name('toduru.update.index');
Route::put('/toduru/update/{toduruId}',\App\Http\Controllers\Toduru\Update\PutController::class)->name('toduru.update.put');
Route::delete('/toduru/delete/{toduruId}',\App\Http\Controllers\Toduru\DeleteController::class)->name('toduru.delete');

require __DIR__.'/auth.php';
