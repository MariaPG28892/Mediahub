@extends('layouts.app')

@section('titulo', 'Series')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-secciones.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/serie/index.js') }}"></script>
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="barra-busqueda barra-unificada">
        {{-- BUSCADOR --}}
        <form method="GET" action="{{ route('buscar_serie') }}" class="form-busqueda">
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

                <option value="10759" {{ request()->segment(3) == 10759 ? 'selected' : '' }}>
                    Acción & Aventura
                </option>

                <option value="16" {{ request()->segment(3) == 16 ? 'selected' : '' }}>
                    Animación
                </option>

                <option value="35" {{ request()->segment(3) == 35 ? 'selected' : '' }}>
                    Comedia
                </option>

                <option value="80" {{ request()->segment(3) == 80 ? 'selected' : '' }}>
                    Crimen
                </option>

                <option value="99" {{ request()->segment(3) == 99 ? 'selected' : '' }}>
                    Documental
                </option>

                <option value="18" {{ request()->segment(3) == 18 ? 'selected' : '' }}>
                    Drama
                </option>

                <option value="10751" {{ request()->segment(3) == 10751 ? 'selected' : '' }}>
                    Familiar
                </option>

                <option value="10762" {{ request()->segment(3) == 10762 ? 'selected' : '' }}>
                    Infantil
                </option>

                <option value="9648" {{ request()->segment(3) == 9648 ? 'selected' : '' }}>
                    Misterio
                </option>

                <option value="10763" {{ request()->segment(3) == 10763 ? 'selected' : '' }}>
                    Noticias
                </option>

                <option value="10764" {{ request()->segment(3) == 10764 ? 'selected' : '' }}>
                    Reality
                </option>

                <option value="10765" {{ request()->segment(3) == 10765 ? 'selected' : '' }}>
                    Ciencia ficción & Fantasía
                </option>

                <option value="10766" {{ request()->segment(3) == 10766 ? 'selected' : '' }}>
                    Telenovela
                </option>

                <option value="10767" {{ request()->segment(3) == 10767 ? 'selected' : '' }}>
                    Talk Show
                </option>

                <option value="10768" {{ request()->segment(3) == 10768 ? 'selected' : '' }}>
                    Guerra & Política
                </option>

                <option value="37" {{ request()->segment(3) == 37 ? 'selected' : '' }}>
                    Western
                </option>
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
            <h2>Series Populares</h2>
        @endif
    </div>

    {{-- LISTADO --}}
    <div class="grid-media">
        @if($series->count() > 0)
            @foreach($series as $serie)
                <a href="{{ route('ver_detalle_serie', $serie['id']) }}" class="card-media-link">
                    <div class="card-media">
                        <img src="https://image.tmdb.org/t/p/w500{{ $serie['poster_path'] ?? '/no-poster.png' }}" alt="{{ $serie['name'] ?? 'Sin título' }}">
                        <div class="titulo-media">
                            {{ $serie['name'] ?? 'Sin título' }}
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
        {{ $series->links('pagination::bootstrap-4') }}
    </div>
@endsection