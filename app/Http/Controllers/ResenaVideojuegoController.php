<?php

namespace App\Http\Controllers;

use App\Models\ResenaVideojuego;
use App\Models\Videojuego;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ResenaVideojuegoController extends Controller
{
    /**
     * Función privada para guardar los videojuegos en mi base de datos, uso cache proque aqui si que he tenido que optimizar muchas cosas
     * como los posters e información
     */
    private function guardarJuegoDesdeRawg($rawgId)
    {
        
        $data = cache()->remember("juego_{$rawgId}", 3600, function () use ($rawgId) {
            return Http::timeout(20)
                ->get(config('services.rawg.base_url') . "/games/{$rawgId}", [
                    'key' => config('services.rawg.key'),
                ])
                ->json();
        });

        $juego = Videojuego::updateOrCreate(
            ['rawg_id' => $rawgId],
            [
                'nombre' => $data['name'] ?? '',
                'descripcion' => $data['description_raw'] ?? null,
                'poster' => $data['background_image']
                    ?? $data['background_image_additional']
                    ?? null,
                'rating' => $data['rating'] ?? null,

                'fecha_lanzamiento' => !empty($data['released'])
                    ? Carbon::parse($data['released'])->format('Y-m-d')
                    : null,

                'generos' => collect($data['genres'] ?? [])
                    ->pluck('name')
                    ->values(),

                'plataformas' => collect($data['platforms'] ?? [])
                    ->map(fn($p) => $p['platform']['name'] ?? null)
                    ->filter()
                    ->values(),

                'desarrolladores' => collect($data['developers'] ?? [])
                    ->pluck('name')
                    ->values(),

                'editores' => collect($data['publishers'] ?? [])
                    ->pluck('name')
                    ->values(),

                'tags' => collect($data['tags'] ?? [])
                    ->pluck('name')
                    ->values(),

                'estado' => 'lanzado',
            ]
        );

        return $juego;
    }
    
    /**
     * Función para mostrar un listado de las reseñas que ha hecho el usuario.
     */
    public function index()
    {
        $user = Auth::user();
        $resenas = ResenaVideojuego::with('videojuego')
            ->where('user_id', $user->id)
            ->where('aprobada', true)
            ->orderByDesc('created_at')
            ->get();

        return view('resena.index_videojuegos', compact('resenas'));
    }

    /**
     * Funcion para guardar la rseña que un usuario hace de un videojuego
     */
    public function guardarResenaVideojuego(Request $request)
    {
        $usuario = Auth::user();

        if (!$usuario) return redirect()->route('login');

        $request->validate([
            'videojuego_id' => 'required',
            'contenido' => 'required|string|min:3|max:1000',
        ]);

        // Guardar juego o sacarlo de la base de datos
        $videojuego = Videojuego::where('rawg_id', $request->videojuego_id)->first();

        if (!$videojuego) {
            $videojuego = $this->guardarJuegoDesdeRawg($request->videojuego_id);
        }

        //Guardar reseña
        ResenaVideojuego::create([
            'user_id' => $usuario->id,
            'videojuego_id' => $videojuego->id,
            'contenido' => $request->contenido,
            'aprobada' => false // moderación
        ]);

        return redirect()->route('ver_detalle_videojuego', $request->videojuego_id)->with('success', 'Reseña enviada correctamente');
    }

    /**
     * Función para eliminar una reseña de un videojuego que haya hecho el usuario.
     */
    public function eliminarResenaVideojuego($id)
    {
        $user = Auth::user();

        $resena = ResenaVideojuego::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $resena->delete();

        return redirect()->back()->with('success', 'Reseña eliminada correctamente');
    }

}
