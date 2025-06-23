<?php

namespace UisIts\Oidc\Http\Controllers;

use Illuminate\Http\Request;

class TriCampusDiscoveryController
{
    public function show()
    {
        return view('laravel-oidc::tri-campus-discovery');
    }

    public function update(Request $request)
    {
        dd($request);
    }
}