<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use App\Models\ResenaPelicula;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ResenaPeliculaController extends Controller
{
    /**
     * Función para mostrar el listado de reseñas aprobadas del usuario.
     */
    public function index()
    {
        $user = Auth::user();

        $resenas = ResenaPelicula::with('pelicula')
            ->where('user_id', $user->id)
            ->where('aprobada', true)
            ->orderByDesc('created_at')
            ->get();

        return view('resena.index_peliculas', compact('resenas'));
    }

    /**
     * Función para guardar reseña
     */
    public function guardarResena(Request $request)
    {
        // Usuario autenticado
        $usuario = Auth::user();

        $request->validate([
            'pelicula_id' => 'required',
            'contenido' => 'required|string|min:3|max:1000',
        ]);

        // Obtener datos de la pelicula
        $data = cache()->remember("pelicula_{$request->pelicula_id}", 3600, function () use ($request) {
            return Http::withToken(config('services.tmdb.token'))
                ->get("https://api.themoviedb.org/3/movie/" . $request->pelicula_id)
                ->json();
        });

        // Guardar o actualizar película en mi base de datos para luego usarla en el perfil desde ahí
        $pelicula = Pelicula::updateOrCreate(
            ['tmdb_id' => $request->pelicula_id],
            [
                'titulo' => $data['title'] ?? '',
                'descripcion' => $data['overview'] ?? '',
                'poster' => $data['poster_path'] ?? null,
                'backdrop' => $data['backdrop_path'] ?? null,
                'fecha_estreno' => !empty($data['release_date'])
                    ? Carbon::parse($data['release_date'])->format('Y-m-d')
                    : null,
                'rating' => $data['vote_average'] ?? null,
            ]
        );

        // Guardar reseña
        ResenaPelicula::create([
            'user_id' => $usuario->id,
            'pelicula_id' => $pelicula->id,
            'contenido' => $request->contenido,
            'aprobada' => false
        ]);

        return redirect()->route('ver_detalle_pelicula', $request->pelicula_id)->with('mensaje', 'Reseña enviada correctamente y pendiente de aprobación');
    }

    /**
     * Función para borrar la reseña que ha hecho el usuario
     */
    public function eliminarResena($id)
    {
        $user = Auth::user();
        $resena = ResenaPelicula::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $resena->delete();

        return redirect()->route('index_resena')->with('success', 'Reseña eliminada correctamente');
    }
}
