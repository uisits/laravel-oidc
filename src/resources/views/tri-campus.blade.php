<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <meta name="robots" content="noindex, nofollow">

    <title>Discovery Page - {{ config('app.name') ? ' - ' . config('app.name') : '' }}</title>
</head>
<body>
<header id="customheader" role="banner">
    <img id="Image1" src="{{ asset('/images/illinois-system-logo.svg') }}" alt="Illinois logo" />
</header>
<h1>Select Your University</h1>
<p class="text">
    This service, <span class="serviceName">UIS ITS Apps</span>,
    supports multiple groups associated with the University of Illinois
    System. Select one of the following to go to the appropriate login
    screen.
</p>
<div>
    <form action="{{ route('tri-campus-discovery.update') }}" method="post">
        @csrf
        <fieldset class="no-border">
            <legend>
                <h2>Choose from the following:</h2>
            </legend>
            <p>
                <input type="radio" name="campus"
                       value="{{ \UisIts\Oidc\Enums\Campus::UIC->value }}" required
                >
                University of Illinois Chicago
                </input>
            </p>
            <p>
                <input type="radio" name="campus" value="{{ \UisIts\Oidc\Enums\Campus::UIS->value }}"
                >
                    University of Illinois Springfield
                </input>
            </p>
            <p>
                <input type="radio" name="campus" value="{{ \UisIts\Oidc\Enums\Campus::UIUC->value }}"
                >
                    University of Illinois Urbana-Champaign
                </input>
            </p>
            <p>
                <button type="submit">Submit</button>
            </p>
            @if ($errors->any())
                <div style="color: red">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </fieldset>
    </form>
</div>
</body>
</html>