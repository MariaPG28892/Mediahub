@extends('layouts.app')

@section('title', 'Detalle Película')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-secciones.css') }}">
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="detalle-media-container">
        @if(Auth::user()->role === 'gestor' || Auth::user()->role === 'admin')
                @if($oculta)
                    {{-- MOSTRAR --}}
                    <form method="POST" action="{{ route('pelicula_mostrar') }}">
                        @csrf

                        <input type="hidden" name="pelicula_id" value="{{ $pelicula['id'] }}">

                        <button class="gestor-boton">
                            Mostrar
                        </button>
                    </form>
                @else
                    {{-- OCULTAR --}}
                    <form method="POST" action="{{ route('pelicula_ocultar') }}">
                        @csrf
                        <input type="hidden" name="pelicula_id" value="{{ $pelicula['id'] }}">
                        <button class="gestor-boton">
                            Ocultar
                        </button>
                    </form>
                @endif
            @endif
        <h1 class="detalle-media-titulo">{{ $pelicula['title'] }}</h1>
        <div class="detalle-media-layout">
            {{--IZQUIERDA--}}
            <div class="detalle-media-izquierda">
                <div class="detalle-media-poster">
                    <img src="https://image.tmdb.org/t/p/w500{{ $pelicula['poster_path'] }}">
                </div>
                {{-- ESTADOS --}}
                <div class="detalle-media-actions">
                    <form method="POST" action="{{ route('guardar_estado_pelicula') }}">
                        @csrf
                        <input type="hidden" name="pelicula_id" value="{{ $pelicula['id'] }}">

                        <button class="detalle-media-btn {{ ($estado ?? null) == 'pendiente' ? 'active' : '' }}" name="estado" value="pendiente">
                            <i class="fas fa-bookmark"></i> Pendiente
                        </button>

                        <button class="detalle-media-btn {{ ($estado ?? null) == 'vista' ? 'active' : '' }}" name="estado" value="vista">
                            <i class="fas fa-check"></i> Vista
                        </button>

                        <button class="detalle-media-btn {{ ($estado ?? null) == 'favorito' ? 'active' : '' }}" name="estado" value="favorito">
                            <i class="fas fa-heart"></i> Favorito
                        </button>
                    </form>
                </div>
            </div>

            {{--DERECHA--}}
            <div class="detalle-media-right">
                <div class="detalle-media-box">
                    {{-- SINOPSIS --}}
                    <p class="detalle-media-label">Sinopsis</p>
                    <p class="detalle-media-text">{{ $pelicula['overview'] }}</p>
                    {{-- GÉNEROS --}}
                    <p class="detalle-media-label">Géneros</p>
                    <div class="contenedor-generos">
                        @if($peliculaBD && $peliculaBD->generos->count())
                            @foreach($peliculaBD->generos as $genero)
                                <span class="genero-etiqueta">
                                    {{ $genero->nombre }}
                                </span>
                            @endforeach
                        @else
                            @foreach($pelicula['genres'] ?? [] as $genero)
                                <span class="genero-etiqueta">
                                    {{ $genero['name'] }}
                                </span>
                            @endforeach
                        @endif
                    </div>
                    {{-- FECHA --}}
                    <p class="detalle-media-label">Fecha estreno</p>
                    <p class="detalle-media-text">{{ $pelicula['release_date'] ?? 'N/A' }}</p>
                    {{-- RATING GLOBAL --}}
                    <p class="detalle-media-label">Puntuación global</p>
                    <p class="detalle-media-text">{{ number_format($pelicula['vote_average'] ?? 0, 1) }}/10</p>
                    {{--PUNTUACIÓN Y AÑADIR A LISTA--}}
                    <div class="detalle-media-rating-grid">
                        {{--PUNTUACIÓN--}}
                        <div class="detalle-media-rating-box">
                            <p class="detalle-media-label">Tu puntuación</p>
                            <form method="POST" action="{{ route('guardar_rating') }}">
                                @csrf
                                <input type="hidden" name="pelicula_id" value="{{ $pelicula['id'] }}">
                                <div class="detalle-media-estrellas">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="star{{ $i }}" name="puntuacion" value="{{ $i }}"
                                            {{ (int)$puntuacion === $i ? 'checked' : '' }}>
                                        <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                                    @endfor
                                </div>
                                <button class="detalle-media-btn-primary">
                                    Guardar puntuación
                                </button>
                            </form>
                        </div>
                        {{--AÑADIR A LISTA --}}
                        <div class="detalle-media-list-box">
                            <p class="detalle-media-label">Añadir a lista</p>
                            <form method="POST" action="{{ route('guardar_en_lista') }}">
                                @csrf
                                <input type="hidden" name="pelicula_id" value="{{ $pelicula['id'] }}">
                                <div class="detalle-media-list-row">
                                    <select name="lista_id" class="detalle-media-select">
                                        <option value="">Selecciona...</option>
                                        @foreach($listas as $lista)
                                            <option value="{{ $lista->id }}">{{ $lista->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <button class="detalle-media-btn-primary">
                                        Añadir
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{--ACTORES --}}
                    <div class="detalle-media-section">
                        <p class="detalle-media-label">Reparto</p>
                        <div class="detalle-media-grid">
                            @foreach($actores as $actor)
                                <div class="detalle-media-card">
                                    @if($actor['profile_path'])
                                        <img src="https://image.tmdb.org/t/p/w200{{ $actor['profile_path'] }}">
                                    @else
                                        <img src="{{ Storage::url('default.png') }}">
                                    @endif
                                    <p class="detalle-media-name">{{ $actor['name'] }}</p>
                                    <p class="detalle-media-sub">
                                        {{ $actor['character'] ?? '' }}
                                    </p>
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
            @if ($errors->any())
                <div class="alert alert-danger" style="color: red">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="reseña-form">
                <form method="POST" action="{{ route('guardar_resena') }}">
                    @csrf
                    <input type="hidden" name="pelicula_id" value="{{ $pelicula['id'] }}">
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
                                <img  src="{{ $resena->usuario->foto ? Storage::url($resena->usuario->foto) : Storage::url('default.png') }}" class="usuario-avatar">
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
                        Todavía no hay reseñas para esta película.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
@endsection