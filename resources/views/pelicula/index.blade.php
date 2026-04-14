@extends('layouts.app')

@section('title', 'Películas')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-secciones.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/pelicula/index.js') }}"></script>
@endsection

@section('content')
    @include('layouts.mensajes')
    {{--BARRA BUSQUEDA--}}
    <div class="barra-busqueda barra-unificada">
        {{-- BUSCADOR --}}
        <form method="GET" action="{{ route('buscar_pelicula') }}" class="form-busqueda">
            <input type="text" name="q" value="{{ request('q') ?? $query ?? '' }}" placeholder="Buscar contenido..." class="input-busqueda">
            <button type="submit" class="boton-busqueda">
                Buscar
            </button>
        </form>
        {{-- FILTRO GÉNERO --}}
        <form method="GET" id="formGenero" class="form-busqueda" onsubmit="return enviarGenero()">
            <select name="genero" class="select-genero" id="selectGenero">
                <option value="" {{ request()->segment(3) == null ? 'selected' : '' }}>
                    Todos
                </option>
                <option value="28" {{ request()->segment(3) == 28 ? 'selected' : '' }}>Acción</option>
                <option value="12" {{ request()->segment(3) == 12 ? 'selected' : '' }}>Aventura</option>
                <option value="16" {{ request()->segment(3) == 16 ? 'selected' : '' }}>Animación</option>
                <option value="35" {{ request()->segment(3) == 35 ? 'selected' : '' }}>Comedia</option>
                <option value="80" {{ request()->segment(3) == 80 ? 'selected' : '' }}>Crimen</option>
                <option value="99" {{ request()->segment(3) == 99 ? 'selected' : '' }}>Documental</option>
                <option value="18" {{ request()->segment(3) == 18 ? 'selected' : '' }}>Drama</option>
                <option value="10751" {{ request()->segment(3) == 10751 ? 'selected' : '' }}>Familia</option>
                <option value="14" {{ request()->segment(3) == 14 ? 'selected' : '' }}>Fantasía</option>
                <option value="36" {{ request()->segment(3) == 36 ? 'selected' : '' }}>Historia</option>
                <option value="27" {{ request()->segment(3) == 27 ? 'selected' : '' }}>Terror</option>
                <option value="10402" {{ request()->segment(3) == 10402 ? 'selected' : '' }}>Música</option>
                <option value="9648" {{ request()->segment(3) == 9648 ? 'selected' : '' }}>Misterio</option>
                <option value="10749" {{ request()->segment(3) == 10749 ? 'selected' : '' }}>Romance</option>
                <option value="878" {{ request()->segment(3) == 878 ? 'selected' : '' }}>Ciencia ficción</option>
                <option value="10770" {{ request()->segment(3) == 10770 ? 'selected' : '' }}>TV Movie</option>
                <option value="53" {{ request()->segment(3) == 53 ? 'selected' : '' }}>Suspense</option>
                <option value="10752" {{ request()->segment(3) == 10752 ? 'selected' : '' }}>Bélica</option>
                <option value="37" {{ request()->segment(3) == 37 ? 'selected' : '' }}>Western</option>
            </select>
            <button type="submit" class="boton-busqueda">
                Filtrar
            </button>
        </form>
    </div>

    {{-- TÍTULO --}}
    <div class="seccion-titulo">

        @if(!empty($query))
            <h2>Resultados para: "{{ $query }}"</h2>
        @else
            <h2>Películas populares</h2>
        @endif

    </div>

    {{--LISTADO--}}
    <div class="grid-media">
        @if($peliculas->count() > 0)
            @foreach($peliculas as $pelicula)
                <a href="{{ route('ver_detalle_pelicula', $pelicula['id']) }}" class="card-media-link">
                    <div class="card-media">
                        <img src="https://image.tmdb.org/t/p/w500{{ $pelicula['poster_path'] ?? '/no-poster.png' }}" alt="{{ $pelicula['title'] ?? 'Sin título' }}">
                        <div class="titulo-media">
                            {{ $pelicula['title'] ?? 'Sin título' }}
                        </div>
                    </div>
                </a>
            @endforeach
        @else
            <div class="sin-resultados">
                No se encontraron resultados.
            </div>
        @endif
    </div>
    {{-- PAGINACIÓN --}}
    <div class="paginacion-centro">
        {{ $peliculas->links('pagination::bootstrap-4') }}
    </div>
@endsection