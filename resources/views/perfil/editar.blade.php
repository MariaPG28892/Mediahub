@extends('layouts.app')

@section('title', 'Editar perfil')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="perfil-editar-container">
        <a href="{{ route('perfil_usuario') }}" class="btn-editar" style="margin-bottom:15px;">
            Volver al perfil
        </a>
        <h2>Editar perfil</h2>
        {{--FOTO--}}
        <div class="foto-perfil">
            <img src="{{ $usuario->foto ? Storage::url($usuario->foto) : Storage::url('default.png') }}">
        </div>

        {{-- FORMULARIO PARA ACTUALIZAR EL PERFIL --}}
        <form action="{{ route('actualizar_perfil') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label>Cambiar foto</label>
            <input type="file" name="foto">

            <label>Nombre</label>
            <input type="text" name="name" value="{{ old('name', $usuario->name) }}">

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $usuario->email) }}">

            <label>Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}">

            <label>Biografía</label>
            <textarea name="biografia">{{ old('biografia', $usuario->biografia) }}</textarea>

            <button type="submit">
                Guardar cambios
            </button>
        </form>
    </div>
@endsection