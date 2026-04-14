@extends('layouts.app')

@section('title', 'Contenido oculto')

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
                Contenido oculto
            </h2>
            {{--Según el rol te lleva a uno o a otro--}}
            @php
                $user = auth()->user();
                $ruta = $user->role === 'admin' ? route('admin_index') : route('gestor_index');
            @endphp

            <a href="{{ $ruta }}" class="gestor-atras">
                Volver al panel
            </a>
        </div>
        {{-- LISTA --}}
        <div class="resenas gestor-lista">
            @forelse($ocultos as $item)
                <div class="resena-card gestor-card">
                    {{-- HEADER --}}
                    <div class="resena-header">
                        <div class="resena-info gestor-user">
                            <img  src="{{ $item->imagen ?? asset('img/default.png') }}" class="gestor-avatar">
                            <div class="gestor-user-text">
                                <strong style="color:#00ff88;">
                                    {{ ucfirst($item->tipo) }}
                                </strong>

                                <strong class="gestor-subtext">
                                    {{ $item->titulo ?? 'Contenido no disponible' }}
                                </strong>

                                <small class="gestor-subtext">
                                    ID API: {{ $item->api_id }}
                                </small>
                            </div>
                        </div>
                    </div>

                    {{--Estados--}}
                    <div class="gestor-acciones">
                        {{-- Reedirigir segun el rol--}}
                        @php
                            $routeMostrar = $user->role === 'admin' ? route('contenido_mostrar') : route('contenido_mostrar');
                        @endphp

                        <form method="POST" action="{{ $routeMostrar }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $item->api_id }}">
                            <input type="hidden" name="tipo" value="{{ $item->tipo }}">
                            <button class="gestor-btn gestor-btn-aprobar">
                                Mostrar
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p style="color:white;">
                    No hay contenido oculto.
                </p>
            @endforelse
        </div>
    </div>
@endsection