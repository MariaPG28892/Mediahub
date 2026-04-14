@extends('layouts.app')

@section('title', 'Detalle Juego')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-secciones.css') }}">
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="detalle-media-container">
        @if(Auth::user()->role === 'gestor' || Auth::user()->role === 'admin')
            @if($oculta)
                {{-- MOSTRAR --}}
                <form method="POST" action="{{ route('videojuego_mostrar') }}">
                    @csrf
                    <input type="hidden" name="videojuego_id" value="{{ $juego['id'] }}">
                    <button class="gestor-boton">
                        Mostrar
                    </button>
                </form>
            @else
                {{-- OCULTAR --}}
                <form method="POST" action="{{ route('videojuego_ocultar') }}">
                    @csrf
                    <input type="hidden" name="videojuego_id" value="{{ $juego['id'] }}">

                    <button class="gestor-boton">
                        Ocultar
                    </button>
                </form>
            @endif
        @endif
        <h1 class="detalle-media-titulo">{{ $juego['name'] ?? 'Sin nombre' }}</h1>
        <div class="detalle-media-layout">
            {{--IZQUIERDA--}}
            <div class="detalle-media-izquierda">
                <div class="detalle-media-poster-juego">
                    <img src="{{ $juego['background_image'] ?? asset('images/no-poster.png') }}">
                </div>
                {{--ESTADO--}}
                <div class="detalle-media-actions">
                    <form method="POST" action="{{ route('guardar_estado_videojuego') }}">
                        @csrf
                        <input type="hidden" name="videojuego_id" value="{{ $juego['id'] }}">

                        <button class="detalle-media-btn {{ ($estado ?? null) == 'pendiente' ? 'active' : '' }}"
                            name="estado" value="pendiente">
                            <i class="fas fa-bookmark"></i> Pendiente
                        </button>

                        <button class="detalle-media-btn {{ ($estado ?? null) == 'jugado' ? 'active' : '' }}"
                            name="estado" value="jugado">
                            <i class="fas fa-check"></i> Jugado
                        </button>

                        <button class="detalle-media-btn {{ ($estado ?? null) == 'favorito' ? 'active' : '' }}"
                            name="estado" value="favorito">
                            <i class="fas fa-heart"></i> Favorito
                        </button>
                    </form>
                </div>
            </div>
            {{--DERECHA--}}
            <div class="detalle-media-right">
                <div class="detalle-media-box">
                    {{-- DESCRIPCIÓN --}}
                    <p class="detalle-media-label">Descripción</p>
                    <p class="detalle-media-text">
                        {!! $juego['description_raw'] ?? 'Sin descripción' !!}
                    </p>
                    {{-- GÉNEROS --}}
                    <p class="detalle-media-label">Géneros</p>
                    <div class="contenedor-generos">
                        @foreach($juego['genres'] ?? [] as $genero)
                            <span class="genero-etiqueta">
                                {{ $genero['name'] ?? '' }}
                            </span>
                        @endforeach
                    </div>
                    {{-- FECHA --}}
                    <p class="detalle-media-label">Fecha de lanzamiento</p>
                    <p class="detalle-media-text">{{ $juego['released'] ?? 'N/A' }}</p>
                    {{-- PUNTUACION GLOBAL --}}
                    <p class="detalle-media-label">Puntuación global</p>
                    <p class="detalle-media-text">
                        {{ number_format($juego['rating'] ?? 0, 1) }} / 5
                    </p>

                    {{-- PUNTUACION Y LISTA--}}
                    <div class="detalle-media-rating-grid">
                        {{-- PUNTUACION--}}
                        <div class="detalle-media-rating-box">
                            <p class="detalle-media-label">Tu puntuación</p>
                            <form method="POST" action="{{ route('guardar_rating_videojuego') }}">
                                @csrf
                                <input type="hidden" name="videojuego_id" value="{{ $juego['id'] }}">
                                <div class="detalle-media-estrellas">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="star{{ $i }}" name="puntuacion" value="{{ $i }}"
                                            {{ (int)($puntuacion ?? 0) === $i ? 'checked' : '' }}>
                                        <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                                    @endfor
                                </div>
                                <button class="detalle-media-btn-primary">
                                    Guardar puntuación
                                </button>
                            </form>
                        </div>
                        {{-- LISTA --}}
                        <div class="detalle-media-list-box">
                            <p class="detalle-media-label">Añadir a lista</p>
                            <form method="POST" action="{{ route('videojuego_guardar_lista') }}">
                                @csrf
                                <input type="hidden" name="videojuego_id" value="{{ $juego['id'] }}">
                                <div class="detalle-media-list-row">
                                    <select name="lista_id" class="detalle-media-select" required>
                                        <option value="">Selecciona...</option>
                                            @foreach($listas as $lista)
                                                <option value="{{ $lista->id }}">
                                                    {{ $lista->nombre }}
                                                </option>
                                            @endforeach
                                        </option>
                                    </select>
                                    <button class="detalle-media-btn-primary">
                                        Añadir
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{--PLATAFORMAS--}}
                    <div class="detalle-media-section">
                        <p class="detalle-media-label">Plataformas</p>
                        <div class="detalle-media-grid">
                            @foreach($plataformas as $platform)
                                <div class="detalle-media-card">
                                    <div class="platform-icon">
                                        @if(($platform['slug'] ?? '') === 'pc')
                                            <i class="fas fa-desktop fa-2x"></i>
                                        @elseif(str_contains($platform['slug'] ?? '', 'playstation'))
                                            <i class="fab fa-playstation fa-2x"></i>
                                        @elseif(str_contains($platform['slug'] ?? '', 'xbox'))
                                            <i class="fab fa-xbox fa-2x"></i>
                                        @elseif(str_contains($platform['slug'] ?? '', 'nintendo'))
                                            <i class="fas fa-gamepad fa-2x"></i>
                                        @elseif(str_contains($platform['slug'] ?? '', 'linux'))
                                            <i class="fab fa-linux fa-2x"></i>
                                        @elseif(str_contains($platform['slug'] ?? '', 'mac'))
                                            <i class="fab fa-apple fa-2x"></i>
                                        @else
                                            <i class="fas fa-gamepad fa-2x"></i>
                                        @endif
                                        <p class="detalle-media-name">
                                            {{ $platform['name'] ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--RESEÑAS--}}
        <div class="reseñas-seccion">
            <h2 class="reseñas-title">Reseñas</h2>
            <div class="reseña-form">
                <form method="POST" action="{{ route('guardar_resena_videojuego') }}">
                    @csrf
                    <input type="hidden" name="videojuego_id" value="{{ $juego['id'] }}">
                    <textarea name="contenido" class="reseña-input" placeholder="Escribe tu reseña..." rows="4"></textarea>
                    <button type="submit" class="reseña-btn">
                        <i class="fas fa-paper-plane"></i>
                        Publicar
                    </button>
                </form>
            </div>
        </div>

        {{-- LISTA RESEÑAS --}}
        <div class="reseñas-seccion">
            <div class="reseñas-lista">
                <h4 class="reseñas-title">Lista de reseñas:</h4>
                @forelse($resenas as $resena)
                    <div class="reseña-card">
                        <div class="reseña-header">
                            <div class="usuario-info">
                                <img src="{{ $resena->usuario->foto ? Storage::url($resena->usuario->foto) : Storage::url('default.png') }}" class="usuario-avatar">
                                <div class="usuario-texto">
                                    <strong>
                                        {{ $resena->usuario->name ?? 'Usuario' }}
                                    </strong>
                                </div>
                            </div>
                            <div class="usuario-fecha">
                                {{ $resena->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="reseña-texto">
                            <strong>Reseña:</strong>
                            <p>{{ $resena->contenido }}</p>
                        </div>
                    </div>
                @empty
                    <p class="reseña-texto">
                        Todavía no hay reseñas para este juego.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
@endsection