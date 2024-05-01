<?php

use Azuriom\Plugin\Trad\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your plugin. These
| routes are loaded by the RouteServiceProvider of your plugin within
| a group which contains the "web" middleware group and your plugin name
| as prefix. Now create something great!
|
*/

Route::middleware('can:trad.public')->group(function () {
    Route::get('/', [PublicController::class, 'index'])->name('index');
    Route::get('/{msg_key}', [PublicController::class, 'show'])->name('show');
});

Route::middleware('can:trad.accept')->group(function () {
    Route::post('/update/accept', [PublicController::class, 'accept'])->name('accept');
    Route::post('/update/save', [PublicController::class, 'save'])->name('save');
});
