@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
@endsection

@section('content')

    @include('layouts.mensajes')
    {{-- SERIES --}}
    <div class="perfil-container">
        <div class="cabecera-seccion">
            <h2 style="color:#00f7ff;">Mis listas de series</h2>
            <a href="{{ route('perfil_usuario') }}" class="btn-editar">
                Volver al perfil
            </a>
        </div>

        {{--Crear lista--}}
        <form action="{{ route('crear_lista') }}" method="POST" style="margin-top:20px;">
            @csrf
            <input type="hidden" name="tipo" value="serie">
            <input type="hidden" name="modulo" value="series">

            <input type="text" name="nombre" placeholder="Escribe el nombre de la lista..." required class="input-neon">
            <button type="submit" class="btn-editar">
                Crear lista
            </button>
        </form>
        {{--Filtro --}}
        <form method="GET" style="margin-top:25px;">
            <select name="tipo" onchange="this.form.submit()" class="select-neon">
                <option value="pendientes" {{ $tipo == 'pendientes' ? 'selected' : '' }}>Pendientes</option>
                <option value="vistas" {{ $tipo == 'vistas' ? 'selected' : '' }}>Vistas</option>
                <option value="favoritos" {{ $tipo == 'favoritos' ? 'selected' : '' }}>Favoritos</option>
                <option value="puntuadas" {{ $tipo == 'puntuadas' ? 'selected' : '' }}>Puntuadas</option>
                <optgroup label="Mis listas">
                    @foreach($listas as $listaItem)
                        <option value="{{ $listaItem->id }}" {{ $tipo == $listaItem->id ? 'selected' : '' }}>
                            {{ $listaItem->nombre }} ({{ $listaItem->series_count ?? 0 }})
                        </option>
                    @endforeach
                </optgroup>
            </select>
        </form>

        {{-- Header --}}
        <div class="cabecera-seccion">
            <h3 style="color:#00f7ff; margin:0;">
                @if($tipo === 'pendientes')
                    Series Pendientes
                @elseif($tipo === 'vistas')
                    Series Vistas
                @elseif($tipo === 'favoritos')
                    Series Favoritas
                @elseif($tipo === 'puntuadas')
                    Series Puntuadas
                @else
                    {{ $listas->firstWhere('id', $tipo)?->nombre ?? 'Lista' }}
                @endif
            </h3>
            {{--Borrar lista--}}
            @if(is_numeric($tipo))
                <form action="{{ route('eliminar_lista', $tipo) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres borrar esta lista?')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="modulo" value="series">
                    <button type="submit" class="btn-borrar">
                        Borrar lista
                    </button>
                </form>
            @endif
        </div>

        {{--Listado de peliculas--}}
        <div class="grid-peliculas">
            @forelse($series as $serie)
                <div class="pelicula-item">
                    <div class="card">
                        <a href="{{ route('ver_detalle_serie', $serie->tmdb_id) }}">
                            <img src="https://image.tmdb.org/t/p/w500{{ $serie->poster }}">
                            <span>{{ $serie->titulo }}</span>
                        </a>
                        @if(is_numeric($tipo))
                            <form action="{{ route('editar_lista', [$tipo, 'serie', $serie->id]) }}"  method="POST"  onsubmit="return confirm('¿Quitar esta serie de la lista?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-borrar-mini">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                    {{--Puntuacion--}}
                    @if($tipo === 'puntuadas' && isset($serie->pivot->puntuacion))
                        <div class="estrellas-rosa">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="estrella {{ $i <= $serie->pivot->puntuacion ? 'rellena' : '' }}">★</span>
                            @endfor
                            <span class="texto-puntuacion">{{ $serie->pivot->puntuacion }}/5</span>
                        </div>
                    @endif
                </div>
            @empty
                <p style="color:white;">No hay series en esta sección</p>
            @endforelse
        </div>
    </div>
@endsection