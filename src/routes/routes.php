<?php

use Illuminate\Support\Facades\Route;
use UisIts\Oidc\Http\Controllers\AuthController;

Route::namespace('UisIts\Oidc\Http\Controllers')->middleware(['web'])->group(function () {
    Route::name('login')->get('login', [AuthController::class, 'login']);

    Route::name('callback')->get('/auth/callback', [AuthController::class, 'callback']);

    Route::name('logout')->get('/logout', [AuthController::class, 'logout']);
});
