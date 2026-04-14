<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'MediaHub')</title>
        <link rel="icon" href="{{ Storage::url('favicon.ico') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-layouts.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-login.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        @yield('styles')
    </head>

    <body class="hold-transition sidebar-mini">

    <div class="wrapper">

        {{-- NAVBAR --}}
        @include('layouts.navbar')

        @include('layouts.mensajes')
        
        {{-- CONTENIDO --}}
        <div class="content-wrapper" style="overflow: visible; transform: none;">
            <section class="content">
                @yield('content')
            </section>
        </div>

        {{-- FOOTER --}}
        @include('layouts.footer')

    </div>


    @yield('scripts')

    </body>
</html>