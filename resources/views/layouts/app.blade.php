<?php
	$css = $data['cssPath'];
	$js = $data['jsPath'];
  ?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DAC') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @foreach($css as $path)
        <link rel='stylesheet' href='{{$path}}'>
    @endforeach
    @yield('style')
</head>
<body>
@yield('body')
    <div id="app">
        <main class="py-5">
            @yield('content')
        </main>
    </div>
</body>
{{--@foreach($js as $path)--}}
{{--    <script src='{{ $path }}'></script>--}}
{{--@endforeach--}}

@yield('script')
</html>
