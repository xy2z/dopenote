<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/layout.css" />
        <link rel="stylesheet" href="/css/nav.css" />
        <link rel="stylesheet" href="/css/editor.css" />

        <title>@yield('title', 'Home') | {{ Config::get('app.name') }}</title>
    </head>
    <body>
        @yield('content')
    </body>
</html>
