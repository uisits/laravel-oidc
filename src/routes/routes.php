<?php

use Illuminate\Support\Facades\Route;
use UisIts\Oidc\Actions\AuthHandler;

Route::name('login')->get('login', [AuthHandler::class, 'login']);

Route::name('callback')->get('/auth/callback', [AuthHandler::class, 'callback']);

Route::name('logout')->get('/logout', [AuthHandler::class, 'logout']);
