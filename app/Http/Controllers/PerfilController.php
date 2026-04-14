<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use App\Models\Serie;
use App\Models\Videojuego;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class PerfilController extends Controller
{
    /**
     * Función para el listado del perfil tanto películas, series o videojuegos, como las reseñas y las listas.
     */
    public function index()
    {
        $usuario = Auth::user();

        $peliculasPendientes = $usuario->peliculas()->wherePivot('estado', 'pendiente')->get();
        $peliculasVistas = $usuario->peliculas()->wherePivot('estado', 'vista')->get();
        $peliculasFavoritas = $usuario->peliculas()->wherePivot('estado', 'favorito')->get();
        $resenasPeliculas = $usuario->resenas()
            ->with('pelicula')
            ->where('aprobada', true)
            ->latest()
            ->get()
            ->unique('pelicula_id');

        $seriesPendientes = $usuario->series()->wherePivot('estado', 'pendiente')->get();
        $seriesVistas = $usuario->series()->wherePivot('estado', 'vista')->get();
        $seriesFavoritas = $usuario->series()->wherePivot('estado', 'favorito')->get();
        $resenasSeries = $usuario->resenasSeries()
            ->with('serie')
            ->where('aprobada', true)
            ->latest()
            ->get()
            ->unique('serie_id');

        $videojuegosPendientes = $usuario->videojuegos() ->wherePivot('estado', 'pendiente')->get();
        $videojuegosJugados = $usuario->videojuegos()->wherePivot('estado', 'jugado')->get();
        $videojuegosFavoritos = $usuario->videojuegos()->wherePivot('estado', 'favorito')->get();
        $resenasVideojuegos = $usuario->resenasVideojuegos()
            ->with('videojuego')
            ->where('aprobada', true)
            ->latest()
            ->get()
            ->unique('videojuego_id');

        return view('perfil.index', compact('usuario', 'peliculasPendientes', 'peliculasVistas', 'peliculasFavoritas', 'resenasPeliculas', 'seriesPendientes', 'seriesVistas', 'seriesFavoritas', 'resenasSeries', 'videojuegosPendientes', 'videojuegosJugados', 'videojuegosFavoritos', 'resenasVideojuegos' ));
    }

    /**
     * Función para editar el perfil del usuario.
     */
    public function editar()
    {
        $usuario = Auth::user();

        return view('perfil.editar', compact('usuario'));
    }

    /**
     * Función para actualizar el perfil del usuario
     */
    public function actualizar(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'biografia' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name','email','telefono','biografia']);

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            if ($foto && $foto->isValid()) {
                //borrar foto anterior si existe
                if (!empty($usuario->foto) && Storage::disk('public')->exists($usuario->foto)) {
                    Storage::disk('public')->delete($usuario->foto);
                }
                //crear nombre seguro
                $extension = $foto->getClientOriginalExtension();
                $nombreFoto = "user_" . $usuario->id . "_" . Carbon::now()->format('YmdHis') . "." . $extension;
                //ruta personalizada
                $ruta = 'usuarios/' . $usuario->id . '/foto/' . $nombreFoto;
                //guardar archivo
                Storage::disk('public')->put($ruta, File::get($foto));
                //guardar en BD
                $data['foto'] = $ruta;
            }
        }

        //actualizar usuario
        $usuario->update($data);

        return redirect()->route('editar_perfil')->with('success', 'Perfil actualizado correctamente');
    }

    /**
     * Función de recomendaciones, recomienda al usuario películas, series o videojuegos basado en generos y puntuacion
     */
    public function recomendaciones()
    {
        $user = Auth::user();

        //Peliculas
        $peliculasUsuario = $user->peliculas()->whereIn('pelicula_user.estado', ['vista', 'favorito'])->get();
        $generosPeliculas = $peliculasUsuario
            ->load('generos')
            ->pluck('generos')
            ->flatten()
            ->pluck('id')
            ->countBy()
            ->sortDesc()
            ->keys();

        $recomendadas = Pelicula::whereHas('generos', function ($q) use ($generosPeliculas) {
                $q->whereIn('generos.id', $generosPeliculas);
            })
            ->whereNotIn('id', $peliculasUsuario->pluck('id'))
            ->inRandomOrder()
            ->take(16)
            ->get();

        //Series
        $seriesUsuario = $user->series()->whereIn('serie_user.estado', ['vista', 'favorito'])->get();
        $generosSeries = $seriesUsuario
            ->load('generos')
            ->pluck('generos')
            ->flatten()
            ->pluck('id')
            ->countBy()
            ->sortDesc()
            ->keys();

        $seriesRecomendadas = Serie::whereHas('generos', function ($q) use ($generosSeries) {
                $q->whereIn('generos.id', $generosSeries);
            })
            ->whereNotIn('id', $seriesUsuario->pluck('id'))
            ->inRandomOrder()
            ->take(16)
            ->get();

        //Videojuegos
        $videojuegosUsuario = $user->videojuegos()->whereIn('videojuego_user.estado', ['jugado', 'favorito'])->get();
        //Sacar géneros desde JSON
        $generosVideojuegos = $videojuegosUsuario
            ->pluck('generos')
            ->flatten()
            ->countBy()
            ->sortDesc()
            ->keys();
        // Recomendación
        $videojuegosRecomendados = Videojuego::where(function ($q) use ($generosVideojuegos) {
                foreach ($generosVideojuegos as $genero) {
                    $q->orWhereJsonContains('generos', $genero);
                }
            })
            ->whereNotIn('id', $videojuegosUsuario->pluck('id'))
            ->inRandomOrder()
            ->take(16)
            ->get();

        return view('recomendaciones.index', compact('recomendadas', 'seriesRecomendadas','videojuegosRecomendados'));
    }
}