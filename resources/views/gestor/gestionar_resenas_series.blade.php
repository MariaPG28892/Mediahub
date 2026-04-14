@extends('layouts.app')

@extends('layouts.app')

@section('title', 'Gestión de series')

@section('styles')
<link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-gestion.css') }}">
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="perfil-container gestor-container">
        {{-- HEADER --}}
        <div class="cabecera-seccion gestor-header">
            <h2 style="color:#00ff88; margin:0;">
                Moderar reseñas de series
            </h2>

            <a href="{{ route('gestor_index') }}" class="gestor-atras">
                Volver al panel
            </a>
        </div>
        {{-- LISTA --}}
        <div class="resenas gestor-lista">
            @forelse($resenas as $resena)
                <div class="resena-card gestor-card">
                    <div class="resena-header">
                        <div class="resena-info gestor-user">
                            <img src="{{ $resena->usuario->foto ? Storage::url($resena->usuario->foto) : Storage::url('default.png') }}" class="gestor-avatar">
                            <div class="gestor-user-text">
                                <strong>{{ $resena->usuario->name }}</strong>
                                <strong class="gestor-subtext">
                                    <i class="fas fa-video"></i> {{ $resena->serie->titulo ?? 'Serie' }}
                                </strong>
                                <small class="gestor-subtext">
                                    Reseña:
                                </small>
                            </div>
                        </div>
                    </div>
                    {{-- CONTENIDO DE LA RESEÑA--}}
                    <div class="resena-texto">
                        {{ $resena->contenido }}
                    </div>
                    {{--ESTADIS --}}
                    <div class="gestor-acciones">
                        <form method="POST" action="{{ route('aprobar_resenas', $resena->id) }}">
                            @csrf
                            <button class="gestor-btn gestor-btn-aprobar">
                                Aprobar
                            </button>
                        </form>
                        <form method="POST" action="{{ route('eliminar_resenas_gestor', $resena->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="gestor-btn gestor-btn-rechazar">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p style="color:white;">No hay reseñas pendientes.</p>
            @endforelse
        </div>
    </div>
@endsection