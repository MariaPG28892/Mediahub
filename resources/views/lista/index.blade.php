@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
@endsection

@section('content')

    @include('layouts.mensajes')
    {{-- PELICULAS --}}
    <div class="perfil-container">
        <div class="cabecera-seccion">
            <h2 style="color:#00f7ff;">Mis listas de películas</h2>
            <a href="{{ route('perfil_usuario') }}" class="btn-editar">
                Volver al perfil
            </a>
        </div>

        {{--Crear lista--}}
        <form action="{{ route('crear_lista') }}" method="POST" style="margin-top:20px;">
            @csrf
            <input type="hidden" name="tipo" value="pelicula">
            <input type="hidden" name="modulo" value="peliculas">
            <input type="text" name="nombre" placeholder="Escribe el nombre de la lista..." required class="input-neon">
            <button type="submit" class="btn-editar">
                Crear lista
            </button>
        </form>

        {{-- Filtro --}}
        <form method="GET" style="margin-top:25px;">
            <select name="tipo" onchange="this.form.submit()" class="select-neon">
                <option value="pendientes" {{ $tipo == 'pendientes' ? 'selected' : '' }}>Pendientes</option>
                <option value="vistas" {{ $tipo == 'vistas' ? 'selected' : '' }}>Vistas</option>
                <option value="favoritos" {{ $tipo == 'favoritos' ? 'selected' : '' }}>Favoritos</option>
                <option value="puntuadas" {{ $tipo == 'puntuadas' ? 'selected' : '' }}>Puntuadas</option>

                <optgroup label="Mis listas">
                    @foreach($listas as $listaItem)
                        <option value="{{ $listaItem->id }}" {{ $tipo == $listaItem->id ? 'selected' : '' }}>
                            {{ $listaItem->nombre }} ({{ $listaItem->peliculas_count ?? 0 }})
                        </option>
                    @endforeach
                </optgroup>
            </select>
        </form>

        {{--Header--}}
        <div class="cabecera-seccion">
            <h3 style="color:#00f7ff; margin:0;">
                @if($tipo === 'pendientes')
                    Películas Pendientes
                @elseif($tipo === 'vistas')
                    Películas Vistas
                @elseif($tipo === 'favoritos')
                    Películas Favoritas
                @elseif($tipo === 'puntuadas')
                    Películas Puntuadas
                @else
                    {{ $listas->firstWhere('id', $tipo)?->nombre ?? 'Lista' }}
                @endif
            </h3>

            {{--Borrar lista --}}
            @if(is_numeric($tipo))
                <form action="{{ route('eliminar_lista', $tipo) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres borrar esta lista?')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="modulo" value="peliculas">
                    <button type="submit" class="btn-borrar">
                        Borrar lista
                    </button>
                </form>
            @endif
        </div>

        {{-- Listado --}}
        <div class="grid-peliculas">
            @forelse($peliculas as $pelicula)
                <div class="pelicula-item">
                    <div class="card">
                        <a href="{{ route('ver_detalle_pelicula', $pelicula->tmdb_id) }}">
                            <img src="{{ $pelicula->poster_url }}" alt="{{ $pelicula->titulo }}">
                            <span>{{ $pelicula->titulo }}</span>
                        </a>
                        @if(is_numeric($tipo))
                            <form action="{{ route('editar_lista', [$tipo, 'pelicula', $pelicula->id]) }}" method="POST" onsubmit="return confirm('¿Quitar esta película de la lista?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-borrar-mini">
                                    <i class="fas fa-times"></i>
                                </button>

                            </form>
                        @endif
                    </div>

                    {{-- Puntuación --}}
                    @if($tipo === 'puntuadas' && isset($pelicula->pivot->puntuacion))
                        <div class="estrellas-rosa">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="estrella {{ $i <= $pelicula->pivot->puntuacion ? 'rellena' : '' }}">★</span>
                            @endfor
                            <span class="texto-puntuacion">{{ $pelicula->pivot->puntuacion }}/5</span>
                        </div>
                    @endif
                </div>
            @empty
                <p style="color:white;">No hay películas en esta sección</p>
            @endforelse
        </div>
    </div>
@endsection