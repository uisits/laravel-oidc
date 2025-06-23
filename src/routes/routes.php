<?php

use Illuminate\Support\Facades\Route;
use UisIts\Oidc\Actions\CallbackHandleAction;
use UisIts\Oidc\Actions\LoginAction;
use UisIts\Oidc\Actions\LogoutAction;
use UisIts\Oidc\Actions\TriCampusHandler;

Route::middleware(['web'])->group(function () {
    // UIS setup
    Route::name('login')
        ->get('login', [LoginAction::class, '__invoke']);

    Route::name('callback')
        ->get('/auth/callback', [CallbackHandleAction::class, '__invoke']);

    Route::name('logout')
        ->get('/logout', [LogoutAction::class, '__invoke']);

    // TriCampus setup
    Route::name('tri-campus-discovery.show')
        ->get('/tri-campus-discovery', [TriCampusHandler::class, 'show']);

    Route::name('tri-campus-discovery.update')
        ->post('/tri-campus-discovery', [TriCampusHandler::class, 'update']);
});
