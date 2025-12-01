<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Dashboard') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts 
    <!-- <link rel="preconnect" href="https://fonts.bunny.net"> -->
    <!-- <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" /> -->

    <!-- Styles / Scripts -->
    @vite(['resources/js/auth/dashboard/dashboard.ts', 'resources/css/app.css', 'resources/css/dashboard.css', 'resources/css/icon/myicon.css'])
</head>

<body id="body"></body>

</html>
