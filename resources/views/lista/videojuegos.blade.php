@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
@endsection

@section('content')
    @include('layouts.mensajes')
    {{-- VIDEOJUEGOS --}}
    <div class="perfil-container">
        <div class="cabecera-seccion">
            <h2 style="color:#00f7ff;">Mis listas de videojuegos</h2>
            <a href="{{ route('perfil_usuario') }}" class="btn-editar">
                Volver al perfil
            </a>
        </div>

        {{--Crear lista --}}
        <form action="{{ route('crear_lista') }}" method="POST" style="margin-top:20px;">
            @csrf
            <input type="hidden" name="tipo" value="videojuego">
            <input type="hidden" name="modulo" value="videojuegos">

            <input type="text"  name="nombre" placeholder="Escribe el nombre de la lista..." required class="input-neon">
            <button type="submit" class="btn-editar">
                Crear lista
            </button>
        </form>

        {{--Filtro--}}
        <form method="GET" style="margin-top:25px;">
            <select name="tipo" onchange="this.form.submit()" class="select-neon">
                <option value="pendientes" {{ $tipo == 'pendientes' ? 'selected' : '' }}>Pendientes</option>
                <option value="jugados" {{ $tipo == 'jugados' ? 'selected' : '' }}>Jugados</option>
                <option value="favoritos" {{ $tipo == 'favoritos' ? 'selected' : '' }}>Favoritos</option>
                <option value="puntuados" {{ $tipo == 'puntuados' ? 'selected' : '' }}>Puntuados</option>
                <optgroup label="Mis listas">
                    @foreach($listas as $listaItem)
                        <option value="{{ $listaItem->id }}" {{ $tipo == $listaItem->id ? 'selected' : '' }}>
                            {{ $listaItem->nombre }} ({{ $listaItem->videojuegos_count ?? 0 }})
                        </option>
                    @endforeach
                </optgroup>
            </select>
        </form>
        {{-- Header --}}
        <div class="cabecera-seccion">
            <h3 style="color:#00f7ff; margin:0;">
                @if($tipo === 'pendientes')
                    Videojuegos Pendientes
                @elseif($tipo === 'jugados')
                    Videojuegos Jugados
                @elseif($tipo === 'favoritos')
                    Videojuegos Favoritos
                @elseif($tipo === 'puntuados')
                    Videojuegos Puntuados
                @else
                    {{ $listas->firstWhere('id', $tipo)?->nombre ?? 'Lista' }}
                @endif
            </h3>
            {{--Borrar la lista--}}
            @if(is_numeric($tipo))
                <form action="{{ route('eliminar_lista', $tipo) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres borrar esta lista?')">
                    @csrf
                    @method('DELETE')

                    <input type="hidden" name="modulo" value="videojuegos">
                    <button type="submit" class="btn-borrar">
                        Borrar lista
                    </button>
                </form>
            @endif
        </div>
        {{--Listado videojuegos --}}
        <div class="grid-peliculas">
            @forelse($videojuegos as $videojuego)
                <div class="pelicula-item">
                    <div class="card">
                        <a href="{{ route('ver_detalle_videojuego', $videojuego->rawg_id) }}">
                            <img src="{{ $videojuego->poster ?? asset('images/no-poster.png') }}">
                            <span>{{ $videojuego->nombre }}</span>
                        </a>
                        {{--Quitar videojuego de la lista--}}
                        @if(is_numeric($tipo))
                            <form action="{{ route('editar_lista', [$tipo, 'videojuego', $videojuego->id]) }}" method="POST" onsubmit="return confirm('¿Quitar este videojuego de la lista?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-borrar-mini">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                    {{--Puntuacion--}}
                    @if($tipo === 'puntuados' && isset($videojuego->pivot->puntuacion))
                        <div class="estrellas-rosa">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="estrella {{ $i <= $videojuego->pivot->puntuacion ? 'rellena' : '' }}">★</span>
                            @endfor
                            <span class="texto-puntuacion">
                                {{ $videojuego->pivot->puntuacion }}/5
                            </span>
                        </div>
                    @endif
                </div>
            @empty
                <p style="color:white;">No hay videojuegos en esta sección</p>
            @endforelse
        </div>
    </div>
@endsection