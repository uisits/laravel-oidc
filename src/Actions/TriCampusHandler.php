<?php

namespace UisIts\Oidc\Actions;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use UisIts\Oidc\Exceptions\InvalidaCampusSelectionException;
use UisIts\Oidc\Facades\Oidc;

class TriCampusHandler
{
    public function show()
    {
        return view('laravel-oidc::tri-campus-discovery');
    }

    /**
     * @param Request $request
     * @return void
     * @throws InvalidaCampusSelectionException
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'campus' => 'string|required|in:uic,uiuc,uis'
        ]);
        return match($validated['campus']) {
            'uic' => Oidc::driver('uic')->redirect(),
            'uiuc' => Oidc::driver('uiuc')->redirect(),
            'uis' => Oidc::driver('uis')->redirect(),
            default => throw new InvalidaCampusSelectionException(
                'Invalid Campus Selection'
            )
        };
    }
}