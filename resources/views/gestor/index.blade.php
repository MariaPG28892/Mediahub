@extends('layouts.app')

@section('title', 'Panel de gestor')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-gestion.css') }}">
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="perfil-container gestor-container">
        {{-- HEADER --}}
        <div class="cabecera-seccion">
            <h2 style="color:#00ff88; margin:0;">
                Panel de gestor
            </h2>
        </div>
        {{-- ESTADÍSTICAS --}}
        <div class="grid-resenas gestor-grid">
            <div class="resena-card gestor-card">
                <div class="resena-header">
                    <strong style="color:#00ff88;">Usuarios activos (7 días)</strong>
                </div>
                <div class="resena-texto-principal">
                    {{ $usuariosActivos }} usuarios activos recientemente
                </div>
            </div>
            <div class="resena-card gestor-card">
                <div class="resena-header">
                    <strong style="color:#00ff88;">Total usuarios</strong>
                </div>
                <div class="resena-texto-principal">
                    {{ $totalUsuarios }} usuarios registrados
                </div>
            </div>
            <div class="resena-card gestor-card">
                <div class="resena-header">
                    <strong style="color:#00ff88;">Reseñas pendientes</strong>
                </div>
                <div class="resena-texto-principal">
                    {{ $totalPendientes }} en total por moderar
                    <br>
                    <small>
                        Películas: {{ $peliculasPendientes }} |
                        Series: {{ $seriesPendientes }} |
                        Juegos: {{ $videojuegosPendientes }}
                    </small>
                </div>
            </div>

        </div>
        {{--gestion --}}
        <div class="perfil-seccion">
            <div class="cabecera-seccion">
                <h3 style="color:#00ff88;">Gestión:</h3>
            </div>
            <div class="pestanas gestor-pestanas">
                <a href="{{ route('gestor_peliculas') }}" class="pestana gestor-boton">
                    Gestionar reseña películas
                </a>

                <a href="{{ route('gestor_series') }}" class="pestana gestor-boton">
                    Gestionar reseña series
                </a>

                <a href="{{ route('gestor_videojuegos') }}" class="pestana gestor-boton">
                    Gestionar reseña videojuegos
                </a>

                <a href="{{ route('gestor_usuarios') }}" class="pestana gestor-boton">
                    Gestionar usuarios
                </a>

                <a href="{{ route('contenido_oculto') }}" class="pestana gestor-boton">
                    Gestionar contenido
                </a>
            </div>
        </div>
        {{-- RESEÑAS RECIENTES --}}
        <div class="perfil-seccion gestor-seccion">
            <div class="cabecera-seccion">
                <h3 style="color:#00ff88;">Reseñas recientes</h3>
            </div>
            <div class="resenas">
                {{-- PELÍCULAS --}}
                @foreach($ultimasPeliculas as $resena)
                    <div class="resena-card gestor-card">
                        <div class="resena-info gestor-user">
                            <img src="{{ $resena->usuario->foto ? Storage::url($resena->usuario->foto) : Storage::url('default.png') }}" class="gestor-avatar">
                            <div class="gestor-user-text">
                                <strong>{{ $resena->usuario->name }}</strong>
                                <strong class="gestor-subtext">
                                    <i class="fas fa-film"></i> {{ $resena->pelicula->titulo ?? 'Película' }}
                                </strong>
                            </div>
                        </div>
                        <div class="resena-texto">
                            {{ $resena->contenido }}
                        </div>
                    </div>
                @endforeach
                {{-- SERIES --}}
                @foreach($ultimasSeries as $resena)
                    <div class="resena-card gestor-card">
                        <div class="resena-info gestor-user">
                            <img src="{{ $resena->usuario->foto ? Storage::url($resena->usuario->foto)  : Storage::url('default.png') }}" class="gestor-avatar">
                            <div class="gestor-user-text">
                                <strong>{{ $resena->usuario->name }}</strong>
                                <strong class="gestor-subtext">
                                    <i class="fas fa-video"></i> {{ $resena->serie->titulo ?? 'Serie' }}
                                </strong>
                            </div>
                        </div>
                        <div class="resena-texto">
                            {{ $resena->contenido }}
                        </div>
                    </div>
                @endforeach
                {{-- VIDEOJUEGOS --}}
                @foreach($ultimasVideojuegos as $resena)
                    <div class="resena-card gestor-card">
                        <div class="resena-info gestor-user">
                            <img src="{{ $resena->usuario->foto ? Storage::url($resena->usuario->foto) : Storage::url('default.png') }}" class="gestor-avatar">
                            <div class="gestor-user-text">
                                <strong>{{ $resena->usuario->name }}</strong>
                                <strong class="gestor-subtext">
                                    <i class="fas fa-gamepad"></i> {{ $resena->videojuego->nombre ?? 'Videojuego' }}
                                </strong>
                            </div>
                        </div>
                        <div class="resena-texto">
                            {{ $resena->contenido }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection