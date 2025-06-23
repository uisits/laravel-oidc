<?php

namespace UisIts\Oidc\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use UisIts\Oidc\Enums\Campus;
use UisIts\Oidc\Exceptions\InvalidaCampusSelectionException;
use UisIts\Oidc\Facades\Oidc;

class TriCampusHandler
{
    public function show()
    {
        return view('laravel-oidc::tri-campus-discovery');
    }

    /**
     * @return void
     *
     * @throws InvalidaCampusSelectionException
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'campus' => 'string|required|in:uic,uiuc,uis',
        ]);

        if ($validated['campus'] === Campus::UIS->value) {
            Session::put('oidc.campus', $validated['campus']);

            return Oidc::driver('uis')->redirect();
        }

        if ($validated['campus'] === Campus::UIC->value) {
            Session::put('oidc.campus', $validated['campus']);

            return Oidc::driver('uic')->redirect();
        }

        if ($validated['campus'] === Campus::UIUC->value) {
            Session::put('oidc.campus', $validated['campus']);

            return Oidc::driver('uiuc')->redirect();
        }

        throw new InvalidaCampusSelectionException(
            'Invalid Campus Selection'
        );
    }
}
