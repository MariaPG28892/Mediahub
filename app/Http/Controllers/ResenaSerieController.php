<?php

namespace App\Http\Controllers;

use App\Models\ResenaSerie;
use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ResenaSerieController extends Controller
{
    /**
     * Listado de reseñas de series del usuario
     */
    public function index()
    {
        $user = Auth::user();
        $resenas = ResenaSerie::with('serie')
            ->where('user_id', $user->id)
            ->where('aprobada', true)
            ->orderByDesc('created_at')
            ->get();

        return view('resena.index_series', compact('resenas'));
    }

    /**
     * Función para guardar las reseñas que hace el usuario y guardamos la serie en la base de datos.
     */
    public function guardarResenaSerie(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'serie_id' => 'required',
            'contenido' => 'required|string|min:3|max:1000',
        ]);

        $serie = Serie::where('tmdb_id', $request->serie_id)->first();
        if (!$serie) {
            $data = cache()->remember("serie_{$request->serie_id}", 3600, function () use ($request) {
                return Http::withToken(config('services.tmdb.token'))
                    ->get("https://api.themoviedb.org/3/tv/" . $request->serie_id)
                    ->json();
            });
            //Guardamos la serie en la base de datos
            $serie = Serie::updateOrCreate(
                ['tmdb_id' => $request->serie_id],
                [
                    'titulo' => $data['name'] ?? '',
                    'descripcion' => $data['overview'] ?? '',
                    'poster' => $data['poster_path'] ?? null,
                    'backdrop' => $data['backdrop_path'] ?? null,
                    'fecha_estreno' => !empty($data['first_air_date'])
                        ? Carbon::parse($data['first_air_date'])->format('Y-m-d')
                        : null,
                    'emision' => $data['status'] ?? null,
                    'numero_temporadas' => $data['number_of_seasons'] ?? null,
                    'numero_episodios' => $data['number_of_episodes'] ?? null,
                    'rating' => $data['vote_average'] ?? null,
                ]
            );
        }
        //creamos la reseña en la base de datos
        ResenaSerie::create([ 'user_id' => $usuario->id, 'serie_id' => $serie->id, 'contenido' => $request->contenido, 'aprobada' => false]);

        return redirect()->route('ver_detalle_serie', $request->serie_id)->with('success', 'Reseña enviada correctamente');
    }

    /**
     *Función para eliminar la reseña que ha hecho el usuario de una serie
     */
    public function eliminarResena($id)
    {
        $user = Auth::user();

        $resena = ResenaSerie::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $resena->delete();

        return redirect()->back()->with('success', 'Reseña eliminada correctamente');
    }
}