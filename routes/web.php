<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GestorController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\ListaController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ResenaPeliculaController;
use App\Http\Controllers\ResenaSerieController;
use App\Http\Controllers\SerieController;
use App\Http\Controllers\ResenaVideojuegoController;
use App\Http\Controllers\VideojuegoController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

    /*No autenticados*/
    // Redirigir raíz al login
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::view('/login', 'login.login')->name('login');
    Route::view('/registro', 'login.registro')->name('registro');
    Route::post('/validar-registro', [LoginController::class, 'register'])->name('validar_registro');
    Route::post('/iniciar-sesion', [LoginController::class, 'login'])->name('iniciar_sesion');


    /* Administrador*/
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin_index');
        Route::get('/admin/datos-usuarios', [AdminController::class, 'indexDataTable'])->name('admin_index_data_table');
        Route::get('/admin/usuarios', [AdminController::class, 'usuarios'])->name('admin_usuarios');
        Route::post('/admin/usuarios/{id}/rol', [AdminController::class, 'cambiarRol'])->name('admin_cambiar_rol');
        Route::post('/admin/usuarios/{id}/bloquear', [AdminController::class, 'bloquear'])->name('admin_bloquear');
        Route::post('/admin/usuarios/{id}/desbloquear', [AdminController::class, 'desbloquear'])->name('admin_desbloquear');
        Route::delete('/admin/usuarios/{id}', [AdminController::class, 'eliminar'])->name('admin_eliminar');
        Route::get('/admin/usuarios/buscar', [AdminController::class, 'buscarUsuarios'])->name('admin_buscar_usuarios');
        Route::get('/admin/gestionar-resena/peliculas', [GestorController::class, 'peliculas'])->name('admin_peliculas');
        Route::get('/admin/gestionar-resena/series', [GestorController::class, 'series'])->name('admin_series');
        Route::get('/admin/gestionar-resena/videojuegos', [GestorController::class, 'videojuegos'])->name('admin_videojuegos');
    });

    /* Gestor */
    Route::middleware('role:gestor')->group(function () {
        
        Route::get('/gestor', [GestorController::class, 'index'])->name('gestor_index');
        Route::get('gestionar-resena/peliculas', [GestorController::class, 'peliculas'])->name('gestor_peliculas');
        Route::get('gestionar-resena/series', [GestorController::class, 'series'])->name('gestor_series');
        Route::get('gestionar-resena/videojuegos', [GestorController::class, 'videojuegos'])->name('gestor_videojuegos');
        
        Route::get('/gestor/usuarios', [GestorController::class, 'usuarios'])->name('gestor_usuarios');
        Route::post('/gestor/bloquear-usuario/{id}', [GestorController::class, 'bloquearUsuario'])->name('gestor_bloquear_usuario');
        Route::post('/gestor/desbloquear-usuario/{id}', [GestorController::class, 'desbloquearUsuario'])->name('gestor_desbloquear_usuario');
    });

    /* Usuarios autenticados */
    Route::middleware(['auth'])->group(function () {

        //INICIO
        Route::get('/inicio', [InicioController::class, 'index'])->name('inicio');

        //PERFIL
        Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil_usuario');
        Route::get('/perfil/editar', [PerfilController::class, 'editar'])->name('editar_perfil');
        Route::put('/perfil/actualizar', [PerfilController::class, 'actualizar'])->name('actualizar_perfil');
        Route::get('/recomendaciones', [PerfilController::class, 'recomendaciones'])->name('recomendaciones');

        //PELICULAS
        Route::get('/inicio-peliculas', [PeliculaController::class, 'index'])->name('inicio_peliculas');
        Route::get('/peliculas/{id}', [PeliculaController::class, 'verDetallesPelicula'])->name('ver_detalle_pelicula');
        Route::post('/pelicula/estado', [PeliculaController::class, 'guardarEstadoPelicula'])->name('guardar_estado_pelicula');
        Route::post('/pelicula/rating', [PeliculaController::class, 'guardarRating'])->name('guardar_rating');
        Route::get('/pelicula/buscar', [PeliculaController::class, 'buscarPelicula'])->name('buscar_pelicula');
        Route::get('/peliculas/genero/{genero}', [PeliculaController::class, 'filtrarPorGenero'])->name('peliculas_genero');
        Route::post('/pelicula/lista', [PeliculaController::class, 'guardarEnLista'])->name('guardar_en_lista');

        //SERIES
        Route::get('/series', [SerieController::class, 'index'])->name('inicio_series');
        Route::get('/series/detalle/{id}', [SerieController::class, 'verDetallesSerie'])->name('ver_detalle_serie');
        Route::post('/serie/estado', [SerieController::class, 'guardarEstadoSerie'])->name('guardar_estado_serie');
        Route::post('/serie/rating', [SerieController::class, 'guardarRatingSerie'])->name('guardar_rating_serie');
        Route::get('/series/buscar', [SerieController::class, 'buscarSerie'])->name('buscar_serie');
        Route::get('/series/genero/{genero}', [SerieController::class, 'filtrarPorGenero'])->name('series_genero');
        Route::post('/series/listas/guardar', [SerieController::class, 'guardarSerieEnLista'])->name('serie_guardar_lista');
        Route::get('/recomendaciones-serie', [SerieController::class, 'recomendaciones'])->name('recomendaciones_series');

        //VIDEOJUEGOS
        Route::get('/videojuegos', [VideojuegoController::class, 'index'])->name('inicio_videojuegos');
        Route::get('/videojuegos/buscar', [VideojuegoController::class, 'buscarVideojuego'])->name('buscar_videojuego');
        Route::get('/videojuegos/genero/{genero}', [VideojuegoController::class, 'filtrarPorGenero'])->name('videojuegos_genero');
        Route::get('/juego/{id}', [VideojuegoController::class, 'verDetallesJuego'])->name('ver_detalle_videojuego');
        Route::post('/videojuegos/estado', [VideojuegoController::class, 'guardarEstadoVideojuego'])->name('guardar_estado_videojuego');
        Route::post('/videojuego/listas/guardar', [VideojuegoController::class, 'guardarVideojuegoEnLista'])->name('videojuego_guardar_lista');
        Route::post('/videojuegos/rating', [VideojuegoController::class, 'guardarRatingVideojuego'])->name('guardar_rating_videojuego');

        //RESEÑA-PELICULA
        Route::post('/pelicula/resena-pelicula', [ResenaPeliculaController::class, 'guardarResena'])->name('guardar_resena');
        Route::get('/mis-resenas/peliculas', [ResenaPeliculaController::class, 'index'])->name('index_resena');
        Route::delete('/resenas/{id}', [ResenaPeliculaController::class, 'eliminarResena'])->name('eliminar_resena');

        //RESEÑA-SERIES
        Route::post('/resena-serie', [ResenaSerieController::class, 'guardarResenaSerie'])->name('guardar_resena_serie');
        Route::get('/mis-resenas/series', [ResenaSerieController::class, 'index'])->name('index_resena_serie');
        Route::delete('/resena-serie/{id}', [ResenaSerieController::class, 'eliminarResena'])->name('eliminar_resena_serie');

        //RESEÑA-VIDEOJUEGOS
        Route::post('/videojuegos/resena', [ResenaVideojuegoController::class, 'guardarResenaVideojuego'])->name('guardar_resena_videojuego');
        Route::get('/mis-resenas/videojuegos', [ResenaVideojuegoController::class, 'index'])->name('index_videojuegos_resena');
        Route::delete('/resena-videojuego/{id}', [ResenaVideojuegoController::class, 'eliminarResenaVideojuego'])->name('eliminar_resena_videojuego');

        //LISTAS
        Route::get('/mis-listas/peliculas', [ListaController::class, 'index'])->name('lista_index');
        Route::get('/mis-listas/series', [ListaController::class, 'indexSeries'])->name('lista_series_index');
        Route::get('/mis-listas/videojuegos', [ListaController::class, 'indexVideojuegos'])->name('lista_videojuegos_index');
        Route::post('/mis-listas', [ListaController::class, 'crearLista'])->name('crear_lista');
        Route::delete('/lista/{lista}/{tipo}/{id}', [ListaController::class, 'editarLista'])->name('editar_lista');
        Route::delete('/listas/{lista}', [ListaController::class, 'eliminarLista'])->name('eliminar_lista');

    });

    /*Rutas para admin y gestor compartidas*/
    Route::middleware(['auth', 'role:admin|gestor'])->group(function () {

        // PELÍCULAS
        Route::post('/peliculas/ocultar', [PeliculaController::class, 'ocultarPelicula'])->name('pelicula_ocultar');
        Route::post('/peliculas/mostrar', [PeliculaController::class, 'mostrarPelicula'])->name('pelicula_mostrar');

        // SERIES
        Route::post('/gestor/serie/ocultar', [SerieController::class, 'ocultarSerie'])->name('serie_ocultar');
        Route::post('/gestor/serie/mostrar', [SerieController::class, 'mostrarSerie'])->name('serie_mostrar');

        // VIDEOJUEGOS
        Route::post('/gestor/videojuego/ocultar', [VideojuegoController::class, 'ocultarVideojuego'])->name('videojuego_ocultar');
        Route::post('/gestor/videojuego/mostrar', [VideojuegoController::class, 'mostrarVideojuego'])->name('videojuego_mostrar');

        //CONTENIDO OCULTO
        Route::post('/gestor/resenas/{id}/aprobar', [GestorController::class, 'aprobar'])->name('aprobar_resenas');
        Route::delete('/gestor/resenas/{id}', [GestorController::class, 'eliminar'])->name('eliminar_resenas_gestor');
        Route::get('/contenido-oculto', [GestorController::class, 'contenidoOculto'])->name('contenido_oculto');
        Route::post('/contenido/mostrar', [GestorController::class, 'mostrarContenido'])->name('contenido_mostrar');
    });

    /*logout*/
    Route::post('/logout', [LoginController::class, 'logout'])->name('cerrar_sesion');