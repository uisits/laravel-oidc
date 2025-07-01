<?php

namespace UisIts\Oidc;

use Illuminate\Support\HtmlString;
use RuntimeException;
use UisIts\Oidc\Exceptions\InvalidBuildAssetException;

class ShibbolethAssetManager
{
    public static function css()
    {
        throw_if(
            ! file_exists(__DIR__.'/../dist/shibboleth-oidc.css'),
            new InvalidBuildAssetException('Build CSS file not found!')
        );

        $styles = @file_get_contents(__DIR__.'/../dist/shibboleth-oidc.css');

        if ($styles === false) {
            throw new RuntimeException('Unable to load the Shibboleth OIDC styles.');
        }

        return new HtmlString(<<<HTML
            <style>{$styles}</style>
        HTML);
    }
}
