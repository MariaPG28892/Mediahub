@extends('layouts.app')

@section('title', 'Mediahub')

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-secciones.css') }}">
@endsection

@section('content')
   <div class="arcade-selector">
        {{-- Peliculas --}}
        <a href="{{route('inicio_peliculas')}}" class="arcade-opcion">
            <div class="arcade-categoria"></div>
            <img src="{{Storage::url('pelicula.png')}}" alt="">
            <h3>Películas</h3>
        </a>
        {{-- Series --}}
        <a href="{{route('inicio_series')}}" class="arcade-opcion">
            <div class="arcade-categoria"></div>
            <img src="{{Storage::url('series.png')}}" alt="">
            <h3>Series</h3>
        </a>
        {{-- Videojuegos --}}
        <a href="{{route('inicio_videojuegos')}}" class="arcade-opcion">
            <div class="arcade-categoria"></div>
            <img src="{{ Storage::url('mando.png') }}" alt="">
            <h3>Videojuegos</h3>
        </a>
    </div>
@endsection