@extends('layouts.app')

@section('titulo', 'Detalle Serie')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-secciones.css') }}">
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="detalle-media-container">
        @if(Auth::user()->role === 'gestor' || Auth::user()->role === 'admin')
            @if($oculta)
                {{-- MOSTRAR --}}
                <form method="POST" action="{{ route('serie_mostrar') }}">
                    @csrf
                    <input type="hidden" name="serie_id" value="{{ $serie['id'] }}">
                    <button class="gestor-boton">
                        Mostrar
                    </button>
                </form>
            @else
                {{-- OCULTAR --}}
                <form method="POST" action="{{ route('serie_ocultar') }}">
                    @csrf
                    <input type="hidden" name="serie_id" value="{{ $serie['id'] }}">
                    <button class="gestor-boton">
                        Ocultar
                    </button>
                </form>
            @endif
        @endif
        <h1 class="detalle-media-titulo">{{ $serie['name'] }}</h1>
        <div class="detalle-media-layout">
            {{--IZQUIERDA--}}
            <div class="detalle-media-izquierda">
                <div class="detalle-media-poster-serie">
                    <img src="https://image.tmdb.org/t/p/w500{{ $serie['poster_path'] }}">
                </div>
                {{--ESTADO --}}
                <div class="detalle-media-actions">
                    <form method="POST" action="{{ route('guardar_estado_serie') }}">
                        @csrf
                        <input type="hidden" name="serie_id" value="{{ $serie['id'] }}">

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

            {{-- DERECHA--}}
            <div class="detalle-media-derecha">
                <div class="detalle-media-box">
                    {{-- SINOPSIS --}}
                    <p class="detalle-media-label">Sinopsis</p>
                    <p class="detalle-media-text">{{ $serie['overview'] }}</p>
                    {{-- GÉNEROS TMDB --}}
                    <p class="detalle-media-label">Géneros</p>
                        <div class="contenedor-generos">
                            @if($serieBD && $generosBD->count())
                                @foreach($generosBD as $genero)
                                    <span class="genero-etiqueta">
                                        {{ $genero }}
                                    </span>
                                @endforeach
                            @else
                                @foreach($serie['genres'] ?? [] as $genero)
                                    <span class="genero-etiqueta">
                                        {{ $genero['name'] }}
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    {{-- ESTADO EMISIÓN --}}
                    <p class="detalle-media-label">Estado de emisión</p>
                    <p class="detalle-media-text">{{ $serie['status'] ?? 'Desconocido' }}</p>
                    {{-- INFORMACION --}}
                    <div class="detalle-media-inline-info">
                        <div class="detalle-media-info-item">
                            <p class="detalle-media-label">Temporadas</p>
                            <p class="detalle-media-text">{{ $serie['number_of_seasons'] ?? 'N/A' }}</p>
                        </div>
                        <div class="detalle-media-info-item">
                            <p class="detalle-media-label">Episodios</p>
                            <p class="detalle-media-text">{{ $serie['number_of_episodes'] ?? 'N/A' }}</p>
                        </div>
                        <div class="detalle-media-info-item">
                            <p class="detalle-media-label">Último episodio</p>
                            <p class="detalle-media-text">{{ $serie['last_air_date'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                    {{-- FECHA --}}
                    <p class="detalle-media-label">Estreno</p>
                    <p class="detalle-media-text">{{ $serie['first_air_date'] ?? 'N/A' }}</p>
                    {{-- PUNTUACIÓN GLOBAL --}}
                    <p class="detalle-media-label">Puntuación global</p>
                    <p class="detalle-media-text">{{ number_format($serie['vote_average'] ?? 0, 1) }}/10</p>
                    {{--PUNTUACIÓN Y LISTA--}}
                    <div class="detalle-media-rating-grid">
                        {{--PUNTUACION--}}
                        <div class="detalle-media-rating-box">
                            <p class="detalle-media-label">Tu puntuación</p>
                            <form method="POST" action="{{ route('guardar_rating_serie') }}">
                                @csrf
                                <input type="hidden" name="serie_id" value="{{ $serie['id'] }}">
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
                        {{-- LISTA --}}
                        <div class="detalle-media-list-box">
                            <p class="detalle-media-label">Añadir a lista</p>
                            <form action="{{ route('serie_guardar_lista') }}" method="POST">
                                @csrf
                                <input type="hidden" name="serie_id" value="{{ $serie['id'] }}">
                                <div class="detalle-media-list-row">
                                    <select name="lista_id" class="detalle-media-select" required>
                                        <option value="">Selecciona...</option>
                                        @foreach($listas as $lista)
                                            <option value="{{ $lista->id }}">
                                                {{ $lista->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="detalle-media-btn-primary">
                                        Añadir a lista
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{--ACTORES--}}
                    <div class="detalle-media-section">
                        <p class="detalle-media-label">Reparto</p>
                        <div class="detalle-media-grid">
                            @foreach($actores as $actor)
                                <div class="detalle-media-card">
                                    <img src="{{ $actor['profile_path'] ? 'https://image.tmdb.org/t/p/w200'.$actor['profile_path'] : Storage::url('default.png')}}">
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
        <div class="reseñas-seccion">
            <h2 class="reseñas-title">Reseñas</h2>
            {{-- ENVIAR LA RESEÑA --}}
            <div class="reseña-form">
                <form method="POST" action="{{ route('guardar_resena_serie') }}">
                    @csrf
                    <input type="hidden" name="serie_id" value="{{ $serie['id'] }}">
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
                                <img src="{{ $resena->usuario->foto ? Storage::url($resena->usuario->foto) : Storage::url('default.png') }}"  class="usuario-avatar">
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
                        Todavía no hay reseñas para esta serie.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
@endsection