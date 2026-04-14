@extends('layouts.app')

@section('title', 'Gestión avanzada de usuarios')

@section('styles')
<link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-gestion.css') }}">
@endsection

@section('content')

    <div class="perfil-container gestor-container">
        @include('layouts.mensajes')
        {{-- HEADER --}}
        <div class="cabecera-seccion gestor-header">
            <h2 style="color:#00ff88;">Gestión de usuarios (Admin)</h2>
            <a href="{{ route('admin_index') }}" class="gestor-atras">
                Volver al panel
            </a>
        </div>
        {{-- LISTA USUARIOS --}}
        <div class="resenas gestor-lista">
            @forelse($usuarios as $user)
                    <div class="resena-card gestor-card">
                        {{-- INFORMACIÓN USUARIO --}}
                        <div class="resena-info gestor-user">
                            <img src="{{ $user->foto ? Storage::url($user->foto)  : Storage::url('default.png') }}" class="gestor-avatar">
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
                        {{--cAMBIAR ESTADOS--}}
                        <div class="gestor-acciones">
                            {{-- CAMBIAR ROL --}}
                            <form method="POST" action="{{ route('admin_cambiar_rol', $user->id) }}">
                                @csrf
                                <select name="role" class="gestor-btn select-rol">
                                    <option value="user" @if($user->role=='user') selected @endif>Usuario</option>
                                    <option value="gestor" @if($user->role=='gestor') selected @endif>Gestor</option>
                                    <option value="admin" @if($user->role=='admin') selected @endif>Admin</option>
                                </select>
                                <button class="gestor-btn gestor-btn-azul">
                                    Cambiar rol
                                </button>
                            </form>
                            {{-- BLOQUEAR Y DESBLOQUEAR --}}
                            @if(!$user->bloqueado)
                                <form method="POST" action="{{ route('admin_bloquear', $user->id) }}">
                                    @csrf
                                    <button class="gestor-btn gestor-btn-rechazar">
                                        Bloquear
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin_desbloquear', $user->id) }}">
                                    @csrf
                                    <button class="gestor-btn gestor-btn-aprobar">
                                        Desbloquear
                                    </button>
                                </form>
                            @endif
                            {{-- ELIMINAR USUARIO--}}
                            <form method="POST" action="{{ route('admin_eliminar', $user->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="gestor-btn gestor-btn-rechazar">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
            @empty
                <p style="color:white;">No hay usuarios.</p>
            @endforelse
        </div>
    </div>

@endsection