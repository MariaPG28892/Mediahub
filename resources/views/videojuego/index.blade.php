@extends('layouts.app')

@section('title', 'Videojuegos')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-secciones.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/videojuego/index.js') }}"></script>
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="barra-busqueda barra-unificada">
        {{--BUSCADOR--}}
        <form method="GET" action="{{ route('buscar_videojuego') }}" class="form-busqueda">
            <input type="text" name="q" value="{{ request('q') ?? $query ?? '' }}" placeholder="Buscar videojuegos..." class="input-busqueda">
            <button type="submit" class="boton-busqueda">
                Buscar
            </button>
        </form>
        {{--BUSCADOR GENERO--}}
        <form method="GET" id="formGenero" class="form-busqueda" onsubmit="return enviarGenero()">
            <select name="genero" class="select-genero" id="selectGenero">
                <option value="">
                    Todos los géneros
                </option>
                @foreach($generos as $genero)
                    <option value="{{ $genero['id'] }}"
                        {{ isset($generoId) && $generoId == $genero['id'] ? 'selected' : '' }}>
                        {{ $genero['name'] }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="boton-busqueda">
                Filtrar
            </button>
        </form>
    </div>

    {{--TÍTULO--}}
    <div class="seccion-titulo">
        @if(!empty($query))
            <h2>Resultados para: "{{ $query }}"</h2>
        @elseif(!empty($generoId))
            <h2>
                Juegos de 
                {{ collect($generos)->firstWhere('id', $generoId)['name'] ?? 'género seleccionado' }}
            </h2>
        @else
            <h2>Videojuegos populares</h2>
        @endif
    </div>

    {{--CATÁLOGO--}}
    <div class="grid-media">
        @forelse($juegos as $juego)
            <a href="{{ route('ver_detalle_videojuego', $juego['id']) }}" class="card-media-link"> 
                <div class="card-media">
                    {{--IMAGEN OPTIMIZADA. Si no carga muy lento--}}
                    <img class="img-videojuego" src="{{ str_replace('media/', 'media/crop/600/400/', $juego['background_image']) }}" loading="lazy" alt="{{ $juego['name'] }}">
                    <div class="titulo-media">
                        {{ $juego['name'] }}
                    </div>
                </div>
            </a>
        @empty
            <div class="sin-resultados">
                No se encontraron videojuegos.
            </div>
        @endforelse
    </div>

    {{--PAGINACIÓN--}}
    <div class="paginacion-centro">
        {{ $juegos->links('pagination::bootstrap-4') }}
    </div>
@endsection