@extends('layouts.app')

@section('title', 'Mi perfil')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/perfil/index.js') }}"></script>
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="perfil-container">
        {{--PERFIL--}}
        <div class="perfil-card">
            <div class="perfil-foto">
                <img src="{{ $usuario->foto ? Storage::url($usuario->foto) : Storage::url('default.png') }}">
            </div>

            <div class="perfil-info">
                <h2>{{ $usuario->nombre_usuario }}</h2>
                <p>{{ $usuario->email }}</p>

                @if($usuario->biografia)
                    <h4>Biografía</h4>
                    <p>{{ $usuario->biografia }}</p>
                @endif
                <a href="{{ route('editar_perfil') }}" class="btn-editar">Editar perfil</a>
                <a href="{{ route('recomendaciones') }}" class="btn-recomendaciones">Recomendaciones</a>

                @if($usuario->role === 'gestor')
                    <a href="{{ route('gestor_index') }}" class="btn-gestor">
                        Panel de Gestor
                    </a>
                @endif

                @if($usuario->role === 'admin')
                    <a href="{{ route('admin_index')}}" class="btn-admin">
                        Panel de Admin
                    </a>
                @endif
            </div>
        </div>

        {{--PELÍCULAS--}}
        <div class="perfil-seccion">
            <div class="cabecera-seccion">
                <h3>Mis películas</h3>
                <a href="{{ route('lista_index') }}" class="pestana pestana-vermas">
                    Ver mis listas de películas
                </a>
            </div>
            <div class="pestanas">
                <button class="pestana activa" onclick="cambiarPestanaPeliculas(this, 'pendientes')">
                    Pendientes
                </button>

                <button class="pestana" onclick="cambiarPestanaPeliculas(this, 'vistas')">
                    Vistas
                </button>

                <button class="pestana" onclick="cambiarPestanaPeliculas(this, 'favoritas')">
                    Favoritas
                </button>
            </div>

            {{-- PELÍCULAS PENDIENTES --}}
            <div id="peliculas-pendientes" class="contenido-pestana activo">
                <div class="carousel-wrapper">
                    <button class="boton-carrusel izquierda" onclick="moverCarrusel(this, -1)">‹</button>
                    <div class="carousel">
                        @foreach($peliculasPendientes as $p)
                            <a href="{{ route('ver_detalle_pelicula', $p->tmdb_id) }}" class="card-link">
                                <div class="card">
                                    <img src="{{ $p->poster_url }}">
                                    <span>{{ $p->titulo }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <button class="boton-carrusel derecha" onclick="moverCarrusel(this, 1)">›</button>
                </div>
            </div>

            {{-- PELÍCULAS VISTAS --}}
            <div id="peliculas-vistas" class="contenido-pestana">
                <div class="carousel-wrapper">
                    <button class="boton-carrusel izquierda" onclick="moverCarrusel(this, -1)">‹</button>
                    <div class="carousel">
                        @foreach($peliculasVistas as $p)
                            <a href="{{ route('ver_detalle_pelicula', $p->tmdb_id) }}" class="card-link">
                                <div class="card">
                                    <img src="{{ $p->poster_url }}">
                                    <span>{{ $p->titulo }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <button class="boton-carrusel derecha" onclick="moverCarrusel(this, 1)">›</button>
                </div>
            </div>

            {{-- PELÍCULAS FAVORITAS --}}
            <div id="peliculas-favoritas" class="contenido-pestana">
                <div class="carousel-wrapper">
                    <button class="boton-carrusel izquierda" onclick="moverCarrusel(this, -1)">‹</button>
                    <div class="carousel">
                        @foreach($peliculasFavoritas as $p)
                            <a href="{{ route('ver_detalle_pelicula', $p->tmdb_id) }}" class="card-link">
                                <div class="card">
                                    <img src="{{ $p->poster_url }}">
                                    <span>{{ $p->titulo }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <button class="boton-carrusel derecha" onclick="moverCarrusel(this, 1)">›</button>
                </div>
            </div>
        </div>

        {{--SERIES--}}
        <div class="perfil-seccion">
            <div class="cabecera-seccion">
                <h3>Mis series</h3>
                <a href="{{ route('lista_series_index') }}" class="pestana pestana-vermas">
                    Ver mis listas de series
                </a>
            </div>

            <div class="pestanas">
                <button class="pestana activa" onclick="cambiarPestanaSeries(this, 'pendientes')">
                    Pendientes
                </button>

                <button class="pestana" onclick="cambiarPestanaSeries(this, 'vistas')">
                    Vistas
                </button>

                <button class="pestana" onclick="cambiarPestanaSeries(this, 'favoritas')">
                    Favoritas
                </button>
            </div>

            {{-- SERIES PENDIENTES --}}
            <div id="series-pendientes" class="contenido-pestana activo">
                <div class="carousel-wrapper">
                    <button class="boton-carrusel izquierda" onclick="moverCarruselSeries(this, -1)">‹</button>
                    <div class="carousel">
                        @foreach($seriesPendientes as $serie)
                            <a href="{{ route('ver_detalle_serie', $serie->tmdb_id) }}" class="card-link">
                                <div class="card">
                                    <img src="https://image.tmdb.org/t/p/w500{{ $serie->poster }}">
                                    <span>{{ $serie->titulo }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <button class="boton-carrusel derecha" onclick="moverCarruselSeries(this, 1)">›</button>
                </div>
            </div>

            {{-- SERIES VISTAS --}}
            <div id="series-vistas" class="contenido-pestana">
                <div class="carousel-wrapper">
                    <button class="boton-carrusel izquierda" onclick="moverCarruselSeries(this, -1)">‹</button>
                    <div class="carousel">
                        @foreach($seriesVistas as $serie)
                            <a href="{{ route('ver_detalle_serie', $serie->tmdb_id) }}" class="card-link">
                                <div class="card">
                                    <img src="https://image.tmdb.org/t/p/w500{{ $serie->poster }}">
                                    <span>{{ $serie->titulo }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <button class="boton-carrusel derecha" onclick="moverCarruselSeries(this, 1)">›</button>
                </div>
            </div>

            {{-- SERIES FAVORITAS --}}
            <div id="series-favoritas" class="contenido-pestana">
                <div class="carousel-wrapper">
                    <button class="boton-carrusel izquierda" onclick="moverCarruselSeries(this, -1)">‹</button>
                    <div class="carousel">
                        @foreach($seriesFavoritas as $serie)
                            <a href="{{ route('ver_detalle_serie', $serie->tmdb_id) }}" class="card-link">
                                <div class="card">
                                    <img src="https://image.tmdb.org/t/p/w500{{ $serie->poster }}">
                                    <span>{{ $serie->titulo }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <button class="boton-carrusel derecha" onclick="moverCarruselSeries(this, 1)">›</button>
                </div>
            </div>
        </div>

        <div class="perfil-seccion">
        {{--VIDEOJUEGOS--}}
        <div class="cabecera-seccion">
            <h3>Mis videojuegos</h3>
            <a href="{{ route('lista_videojuegos_index') }}" class="pestana pestana-vermas">
                Ver mis listas de videojuegos
            </a>
        </div>
        <div class="pestanas">
            <button class="pestana activa" onclick="cambiarPestanaVideojuegos(this, 'pendientes')">
                Pendientes
            </button>

            <button class="pestana" onclick="cambiarPestanaVideojuegos(this, 'jugados')">
                Jugados
            </button>

            <button class="pestana" onclick="cambiarPestanaVideojuegos(this, 'favoritos')">
                Favoritos
            </button>
        </div>

        {{-- PENDIENTES --}}
        <div id="videojuegos-pendientes" class="contenido-pestana activo">
            <div class="carousel-wrapper">
                <button class="boton-carrusel izquierda" onclick="moverCarruselVideojuegos(this, -1)">‹</button>
                <div class="carousel">
                    @foreach($videojuegosPendientes as $juego)
                        <a href="{{ route('ver_detalle_videojuego', $juego->rawg_id) }}" class="card-link">
                            <div class="card">
                                <img src="{{ $juego->poster ? $juego->poster : asset('img/no-image.png') }}">
                                <span>{{ $juego->nombre }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button class="boton-carrusel derecha" onclick="moverCarruselVideojuegos(this, 1)">›</button>
            </div>
        </div>

        {{-- JUGADOS --}}
        <div id="videojuegos-jugados" class="contenido-pestana">
            <div class="carousel-wrapper">
                <button class="boton-carrusel izquierda" onclick="moverCarruselVideojuegos(this, -1)">‹</button>
                <div class="carousel">
                    @foreach($videojuegosJugados as $juego)
                        <a href="{{ route('ver_detalle_videojuego', $juego->rawg_id) }}" class="card-link">
                            <div class="card">
                                <img src="{{ $juego->poster ? $juego->poster : asset('img/no-image.png') }}">
                                <span>{{ $juego->nombre }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button class="boton-carrusel derecha" onclick="moverCarruselVideojuegos(this, 1)">›</button>
            </div>
        </div>

        {{-- FAVORITOS --}}
        <div id="videojuegos-favoritos" class="contenido-pestana">
            <div class="carousel-wrapper">
                <button class="boton-carrusel izquierda" onclick="moverCarruselVideojuegos(this, -1)">‹</button>
                <div class="carousel">
                    @foreach($videojuegosFavoritos as $juego)
                        <a href="{{ route('ver_detalle_videojuego', $juego->rawg_id) }}" class="card-link">
                            <div class="card">
                                <img src="{{ $juego->poster ? $juego->poster : asset('img/no-image.png') }}">
                                <span>{{ $juego->nombre }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button class="boton-carrusel derecha" onclick="moverCarruselVideojuegos(this, 1)">›</button>
            </div>
        </div>
    </div>

    {{--Reseña peliculas--}}
    <div class="perfil-seccion">
        <div class="cabecera-seccion">
            <h3>Mis reseñas de películas</h3>
            <a href="{{ route('index_resena') }}" class="pestana pestana-vermas">
                Ver reseñas películas
            </a>
        </div>
        <div class="resenas">
            @forelse($resenasPeliculas->take(2) as $r)
                <div class="resena-card">
                    <div class="resena-header">
                        <div class="resena-info">
                            <img src="{{ $r->pelicula?->poster_url ?? asset('images/no-poster.png') }}">
                            <strong class="resena-titulo">
                                {{ $r->pelicula?->titulo ?? 'Película eliminada' }}
                            </strong>
                        </div>
                        <span class="resena-fecha">
                            {{ $r->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="resena-texto">
                        {{ $r->contenido }}
                    </div>
                </div>
            @empty
                <p class="empty-msg">No has escrito reseñas aún</p>
            @endforelse
        </div>
    </div>
    {{-- Reseñas series --}}
    <div class="perfil-seccion">
        <div class="cabecera-seccion">
            <h3>Mis reseñas de series</h3>
            <a href="{{ route('index_resena_serie') }}" class="pestana pestana-vermas">
                Ver reseñas series
            </a>
        </div>
        <div class="resenas">
            @forelse($resenasSeries->take(2) as $r)
                <div class="resena-card">
                    <div class="resena-header">
                        <div class="resena-info">
                             <img src="{{ $r->serie?->poster? 'https://image.tmdb.org/t/p/w500' . $r->serie->poster : asset('images/no-poster.png') }}">
                            <strong class="resena-titulo">
                                {{ $r->serie?->titulo ?? 'Serie eliminada' }}
                            </strong>
                        </div>
                        <span class="resena-fecha">
                            {{ $r->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="resena-texto">
                        {{ $r->contenido }}
                    </div>
                </div>
            @empty
                <p class="empty-msg">No has escrito reseñas aún</p>
            @endforelse
        </div>
    </div>
    <div class="perfil-seccion">

    {{--Reseña Videojuegos--}}
    <div class="cabecera-seccion">
        <h3>Mis reseñas de videojuegos</h3>
        <a href="{{route('index_videojuegos_resena')}}" class="pestana pestana-vermas">
            Ver reseñas videojuegos
        </a>
    </div>
    <div class="resenas">
        @forelse($resenasVideojuegos->take(2) as $r)
            <div class="resena-card">
                <div class="resena-header">
                    <div class="resena-info">
                        <img src="{{ $r->videojuego?->poster ?? asset('images/no-poster.png') }}">
                        <strong class="resena-titulo">
                            {{ $r->videojuego?->nombre ?? 'Juego eliminado' }}
                        </strong>
                    </div>
                    <span class="resena-fecha">
                        {{ $r->created_at->format('d/m/Y') }}
                    </span>
                </div>
                <div class="resena-texto">
                    {{ $r->contenido }}
                </div>
            </div>
        @empty
            <p class="empty-msg">No has escrito reseñas aún</p>
        @endforelse
    </div>
    <form action="{{ route('perfil_eliminar') }}" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar tu cuenta? Esta acción no se puede deshacer.')">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn-borrar">
            Eliminar cuenta
        </button>
    </form>
@endsection