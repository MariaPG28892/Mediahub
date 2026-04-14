@extends('layouts.app')

@section('title', 'Mis reseñas')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="perfil-container">
        {{--HEADER--}}
        <div class="cabecera-seccion">
            <h2 style="color:#00f7ff; margin:0;">
                Mis reseñas de películas
            </h2>
            <a href="{{ route('perfil_usuario') }}" class="btn-editar">
                Volver al perfil
            </a>
        </div>
        <div class="grid-resenas">
            @forelse($resenas as $resena)
                <div class="resena-card">
                    <div class="resena-header">
                        {{-- INFORMACIÓN--}}
                        <div class="resena-info">
                            <img src="{{ $resena->pelicula?->poster_url ?? asset('images/no-poster.png') }}">
                            <div>
                                <strong class="resena-titulo">
                                    {{ $resena->pelicula?->titulo ?? 'Película eliminada' }}
                                </strong>
                            </div>
                        </div>
                        {{-- FECHA Y BORRAR --}}
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span class="resena-fecha">
                                {{ $resena->created_at->format('d/m/Y') }}
                            </span>
                            <form action="{{ route('eliminar_resena', $resena->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta reseña?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-borrar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    {{-- CONTENIDO --}}
                    <div class="resena-texto">
                        {{ $resena->contenido }}
                    </div>
                </div>
            @empty
                <p style="color:white; margin-top:20px;">
                    No has escrito reseñas aún
                </p>
            @endforelse
        </div>
    </div>  
@endsection