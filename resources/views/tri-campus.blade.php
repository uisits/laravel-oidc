<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>Discovery Page - {{ config('app.name') ? ' Shibboleth OIDC ' . config('app.name') : '' }}</title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    {{ \UisIts\Oidc\ShibbolethAssetManager::css() }}
</head>
<body class="h-full ">

<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <img class="mx-auto h-10 w-auto" src="{{ asset('/images/illinois-system-logo.svg') }}" alt="UIllinois Logo"/>
        <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Choose a campus to login</h2>
    </div>

    <p class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px] text-center text-sm/6 text-gray-500">
        This service, <span class="font-bold">UIS ITS Apps</span>,
        supports multiple groups associated with the University of Illinois
        System. Select one of the following to go to the appropriate login
        screen.
    </p>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="bg-white px-6 py-12 shadow-sm sm:rounded-lg sm:px-12">
            <form class="space-y-6" action="{{ route('tri-campus-discovery.update') }}" method="POST">
                <fieldset aria-label="Server size">
                    @csrf
                    <div class="space-y-4">
                        <label aria-label="{{ \UisIts\Oidc\Enums\Campus::UIS->name }}"
                               aria-description="{{ \UisIts\Oidc\Enums\Campus::UIS->name }}"
                               class="group relative block rounded-lg border border-gray-300 bg-white px-6 py-4 has-checked:outline-2 has-checked:-outline-offset-2 has-checked:outline-indigo-600 has-focus-visible:outline-3 has-focus-visible:-outline-offset-1 sm:flex sm:justify-between">
                            <input type="radio" name="campus" value="{{ \UisIts\Oidc\Enums\Campus::UIS->value }}"
                                   class="absolute inset-0 appearance-none focus:outline-none"/>
                            <span class="flex items-center">
                                <span class="flex flex-col text-sm">
                                  <span class="font-medium text-gray-900">University of Illinois Springfield</span>
                                </span>
                              </span>
                        </label>
                        <label aria-label="{{ \UisIts\Oidc\Enums\Campus::UIC->name }}"
                               aria-description="{{ \UisIts\Oidc\Enums\Campus::UIC->name }}"
                               class="group relative block rounded-lg border border-gray-300 bg-white px-6 py-4 has-checked:outline-2 has-checked:-outline-offset-2 has-checked:outline-indigo-600 has-focus-visible:outline-3 has-focus-visible:-outline-offset-1 sm:flex sm:justify-between">
                            <input type="radio" name="campus" value="{{ \UisIts\Oidc\Enums\Campus::UIC->value }}"
                                   class="absolute inset-0 appearance-none focus:outline-none"/>
                            <span class="flex items-center">
                                <span class="flex flex-col text-sm">
                                  <span class="font-medium text-gray-900">University of Illinois Chicago</span>
                              </span>
                            </span>
                        </label>
                        <label aria-label="{{ \UisIts\Oidc\Enums\Campus::UIUC->name }}"
                               aria-description="{{ \UisIts\Oidc\Enums\Campus::UIC->name }}"
                               class="group relative block rounded-lg border border-gray-300 bg-white px-6 py-4 has-checked:outline-2 has-checked:-outline-offset-2 has-checked:outline-indigo-600 has-focus-visible:outline-3 has-focus-visible:-outline-offset-1 sm:flex sm:justify-between">
                            <input type="radio" name="campus" value="{{ \UisIts\Oidc\Enums\Campus::UIUC->value }}"
                                   class="absolute inset-0 appearance-none focus:outline-none"/>
                            <span class="flex items-center">
                                <span class="flex flex-col text-sm">
                                  <span class="font-medium text-gray-900">University of Illinois Urbana-Champaign</span>
                                </span>
                              </span>
                        </label>
                    </div>
                </fieldset>

                <div>
                    <button type="submit"
                            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{--<header role="banner">--}}
{{--    <img src="{{ asset('/images/illinois-system-logo.svg') }}" alt="Illinois logo" />--}}
{{--</header>--}}

{{--<h1 class="text-3xl font-mono text-blue-500">Select Your University</h1>--}}
{{--<p class="text">--}}
{{--    This service, <span class="serviceName">UIS ITS Apps</span>,--}}
{{--    supports multiple groups associated with the University of Illinois--}}
{{--    System. Select one of the following to go to the appropriate login--}}
{{--    screen.--}}
{{--</p>--}}
{{--<div>--}}
{{--    <form action="{{ route('tri-campus-discovery.update') }}" method="post">--}}
{{--        @csrf--}}
{{--        <fieldset class="no-border">--}}
{{--            <legend>--}}
{{--                <h2>Choose from the following:</h2>--}}
{{--            </legend>--}}
{{--            <p>--}}
{{--                <input type="radio" name="campus"--}}
{{--                       value="{{ \UisIts\Oidc\Enums\Campus::UIC->value }}" required--}}
{{--                >--}}
{{--                University of Illinois Chicago--}}
{{--                </input>--}}
{{--            </p>--}}
{{--            <p>--}}
{{--                <input type="radio" name="campus" value="{{ \UisIts\Oidc\Enums\Campus::UIS->value }}"--}}
{{--                >--}}
{{--                    University of Illinois Springfield--}}
{{--                </input>--}}
{{--            </p>--}}
{{--            <p>--}}
{{--                <input type="radio" name="campus" value="{{ \UisIts\Oidc\Enums\Campus::UIUC->value }}"--}}
{{--                >--}}
{{--                    University of Illinois Urbana-Champaign--}}
{{--                </input>--}}
{{--            </p>--}}
{{--            <p>--}}
{{--                <button type="submit">Submit</button>--}}
{{--            </p>--}}
{{--            @if ($errors->any())--}}
{{--                <div style="color: red">--}}
{{--                    <ul>--}}
{{--                        @foreach ($errors->all() as $error)--}}
{{--                            <li>{{ $error }}</li>--}}
{{--                        @endforeach--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--        </fieldset>--}}
{{--    </form>--}}
{{--</div>--}}
</body>
</html>