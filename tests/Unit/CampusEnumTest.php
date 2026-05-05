<?php

use UisIts\Oidc\Enums\Campus;

test('campus enum has correct string values', function () {
    expect(Campus::UIS->value)->toBe('uis')
        ->and(Campus::UIC->value)->toBe('uic')
        ->and(Campus::UIUC->value)->toBe('uiuc');
});

test('campus enum has exactly three cases', function () {
    expect(Campus::cases())->toHaveCount(3);
});

test('campus can be created from valid value', function (string $value, Campus $expected) {
    expect(Campus::from($value))->toBe($expected);
})->with([
    ['uis', Campus::UIS],
    ['uic', Campus::UIC],
    ['uiuc', Campus::UIUC],
]);

test('campus tryFrom returns null for invalid value', function () {
    expect(Campus::tryFrom('invalid'))->toBeNull()
        ->and(Campus::tryFrom(''))->toBeNull()
        ->and(Campus::tryFrom('UIS'))->toBeNull();
});

test('campus from throws for invalid value', function () {
    expect(fn () => Campus::from('invalid'))->toThrow(\ValueError::class);
});
