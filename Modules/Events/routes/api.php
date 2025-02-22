<?php

use Illuminate\Support\Facades\Route;
use Modules\Events\Http\Controllers\Api\Events\EventController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::middleware(['auth:sanctum','verified_email'])->prefix('events')->group(function () {
    Route::controller(EventController::class)->group(function(){
        Route::get('/', 'index');
        Route::post('/create', 'admitCreate')->name('event.create');
        Route::post('/admit', 'admitStore')->name('event.admit');
    });
});