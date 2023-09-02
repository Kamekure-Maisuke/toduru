<?php

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
Route::get('/sample',[\App\Http\Controllers\Sample\IndexController::class,'show']);
Route::get('/sample/{id}',[\App\Http\Controllers\Sample\IndexController::class,'showId']);
Route::get('/toduru',\App\Http\Controllers\Toduru\IndexController::class)->name('toduru.index');
Route::post('/toduru/create',\App\Http\Controllers\Toduru\CreateController::class)->name('toduru.create');
Route::get('/toduru/update/{toduruId}',\App\Http\Controllers\Toduru\Update\IndexController::class)->name('toduru.update.index');
Route::put('/toduru/update/{toduruId}',\App\Http\Controllers\Toduru\Update\PutController::class)->name('toduru.update.put');
Route::delete('/toduru/delete/{toduruId}',\App\Http\Controllers\Toduru\DeleteController::class)->name('toduru.delete');
