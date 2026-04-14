@extends('layouts.app')

@section('title', 'Panel Admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-gestion.css') }}">
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="perfil-container gestor-container">
        {{-- HEADER --}}
        <div class="cabecera-seccion">
            <h2 style="color:#00ff88; margin:0;">
                Panel de administrador
            </h2>
        </div>
        {{-- ESTADÍSTICAS DE USUARIOS Y RESEÑAS--}}
        <div class="grid-resenas gestor-grid">
            <div class="resena-card gestor-card">
                <div class="resena-header">
                    <strong style="color:#00ff88;">Usuarios activos (7 días)</strong>
                </div>
                <div class="resena-texto-principal">
                    {{ $usuariosActivos }} usuarios activos recientemente
                </div>
            </div>
            <div class="resena-card gestor-card">
                <div class="resena-header">
                    <strong style="color:#00ff88;">Total usuarios</strong>
                </div>
                <div class="resena-texto-principal">
                    {{ $totalUsuarios }} usuarios registrados
                </div>
            </div>
            <div class="resena-card gestor-card">
                <div class="resena-header">
                    <strong style="color:#00ff88;">Reseñas pendientes</strong>
                </div>
                <div class="resena-texto-principal">
                    {{ $totalResenasPendientes }} en total
                </div>
            </div>
            <div class="resena-card gestor-card">
                <div class="resena-header">
                    <strong style="color:#00ff88;">Usuarios bloqueados</strong>
                </div>
                <div class="resena-texto-principal">
                    {{ $bloqueados }} usuarios bloqueados
                </div>
            </div>
        </div>

        {{-- SECCIÓN DE GESTIÓN DE ADMINISTRADOR --}}
        <div class="perfil-seccion">
            <div class="cabecera-seccion">
                <h3 style="color:#00ff88;">Gestión:</h3>
            </div>
            <div class="pestanas gestor-pestanas">
                <a href="{{route('admin_usuarios')}}" class="pestana gestor-boton">
                    Gestión de usuarios
                </a>

                <a href="{{route('admin_index_data_table')}}" class="pestana gestor-boton">
                    Tabla de datos de usuarios
                </a>

                <a href="{{ route('admin_peliculas') }}" class="pestana gestor-boton">
                    Reseñas de películas
                </a>

                <a href="{{ route('admin_series') }}" class="pestana gestor-boton">
                    Reseñas de series
                </a>

                <a href="{{ route('admin_videojuegos') }}" class="pestana gestor-boton">
                    Reseñas de videojuegos
                </a>

                <a href="{{ route('contenido_oculto') }}" class="pestana gestor-boton">
                    Contenido oculto
                </a>
            </div>

        </div>
    </div>
@endsection