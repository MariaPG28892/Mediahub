@extends('layouts.app')

@section('title', 'Recomendaciones')

@section('styles')
<link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="perfil-container">
        {{--Header --}}
        <div class="cabecera-seccion">
            <h2 style="color:#00f7ff; margin:0;">
                Recomendaciones
            </h2>
            <a href="{{ route('perfil_usuario') }}" class="btn-editar">
                Volver al perfil
            </a>
        </div>

        {{-- PELÍCULAS --}}
        <div class="cabecera-seccion">
            <h3 style="color:#00f7ff; margin-top:20px;">
                Películas recomendadas para ti
            </h3>
        </div>

        <div class="grid-peliculas">
            @forelse($recomendadas as $p)
                <a href="{{ route('ver_detalle_pelicula', $p->tmdb_id) }}" class="card-link">
                    <div class="card">
                        <img src="{{ $p->poster_url ?? '' }}">
                        <span>{{ $p->titulo }}</span>
                        <div style="color:#00f7ff;" class="card-recomendados">
                            <i class="fas fa-star"></i> {{ $p->rating }}
                        </div>
                    </div>
                </a>
                @empty
                <p style="color:white; margin-top:20px;">
                    No hay recomendaciones de películas.
                </p>
            @endforelse
        </div>

        {{-- SERIES --}}
        <div class="cabecera-seccion">
            <h3 style="color:#ff00ff; margin-top:40px;">
                Series recomendadas para ti
            </h3>
        </div>
        <div class="grid-peliculas">

            @forelse($seriesRecomendadas as $s)
                <a href="{{ route('ver_detalle_serie', $s->tmdb_id) }}" class="card-link">
                    <div class="card">
                        <img src="https://image.tmdb.org/t/p/w500{{ $s->poster }}">
                        <span>{{ $s->titulo }}</span>
                        <div style="color:#ff00ff;" class="card-recomendados">
                            <i class="fas fa-star"></i> {{ $s->rating }}
                        </div>
                    </div>
                </a>
                @empty
                <p style="color:white; margin-top:20px;">
                    No hay recomendaciones de series disponibles.
                </p>
            @endforelse
        </div>

        {{-- VIDEOJUEGOS--}}
        <div class="cabecera-seccion">
            <h3 style="color:#00ff88; margin-top:40px;">
                Videojuegos recomendados para ti
            </h3>
        </div>

        <div class="grid-peliculas">

            @forelse($videojuegosRecomendados as $v)
                <a href="{{ route('ver_detalle_videojuego', $v->rawg_id) }}" class="card-link">
                    <div class="card">
                        <img src="{{ $v->poster ?? asset('images/no-poster.png') }}">
                        <span>{{ $v->nombre }}</span>
                        <div style="color:#00ff88;" class="card-recomendados">
                            <i class="fas fa-star"></i> {{ $v->rating }}
                        </div>
                    </div>
                </a>
                @empty
                <p style="color:white; margin-top:20px;">
                    No hay recomendaciones de videojuegos.
                </p>
            @endforelse
        </div>
    </div>
@endsection