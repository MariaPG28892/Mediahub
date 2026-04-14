@extends('layouts.app')

@section('title', 'Gestión de usuarios')

@section('styles')
<link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-gestion.css') }}">
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="perfil-container gestor-container">
        {{-- HEADER --}}
        <div class="cabecera-seccion gestor-header">
            <h2 style="color:#00ff88;">Gestión de usuarios</h2>
            <a href="{{ route('gestor_index') }}" class="gestor-atras">
                Volver al panel
            </a>
        </div>
        {{-- LISTA USUARIOS --}}
        <div class="resenas gestor-lista">
            @forelse($usuarios as $user)
                @if($user->role !== 'admin')
                    <div class="resena-card gestor-card">
                        {{-- INFORMACIÓN USUARIO --}}
                        <div class="resena-info gestor-user">
                            <img src="{{ $user->foto ? Storage::url($user->foto) : Storage::url('default.png') }}" class="gestor-avatar">
                            <div class="gestor-user-text">
                                <strong>{{ $user->nombre_usuario }}</strong>
                                <small class="gestor-subtext">
                                    Nombre Completo: {{ $user->name }}
                                </small>
                                <small class="gestor-subtext">
                                    Email: {{ $user->email }}
                                </small>
                                <small class="gestor-subtext">
                                    Rol: {{ $user->role }}
                                </small>
                                <small class="gestor-subtext">
                                    Estado: 
                                    @if($user->bloqueado)
                                        <span style="color:#ff4d6d;">Bloqueado</span>
                                    @else
                                        <span style="color:#00ff88;">Activo</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                        {{--ESTADOS--}}
                        <div class="gestor-acciones">
                            {{-- BLOQUEAR Y DESBLOQUEAR --}}
                            @if(!$user->bloqueado)
                                <form method="POST" action="{{ route('gestor_bloquear_usuario', $user->id) }}">
                                    @csrf
                                    <button class="gestor-btn gestor-btn-rechazar">
                                        Bloquear
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('gestor_desbloquear_usuario', $user->id) }}">
                                    @csrf
                                    <button class="gestor-btn gestor-btn-aprobar">
                                        Desbloquear
                                    </button>
                                </form>
                            @endif
                            {{-- SOLO ADMIN --}}
                            @if(auth()->user()->role === 'admin')
                                {{-- HACER GESTOR --}}
                                <form method="POST" action="#">
                                    @csrf
                                    <button class="gestor-btn">
                                        Hacer gestor
                                    </button>
                                </form>
                                {{-- ELIMINAR --}}
                                <form method="POST" action="#">
                                    @csrf
                                    @method('DELETE')
                                    <button class="gestor-btn gestor-btn-rechazar">
                                        Eliminar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            @empty
                <p style="color:white;">No hay usuarios.</p>
            @endforelse
        </div>
    </div>
@endsection