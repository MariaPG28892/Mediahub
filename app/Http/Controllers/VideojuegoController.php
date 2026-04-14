<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\ContenidoControl;
use App\Models\ResenaVideojuego;
use App\Models\Videojuego;
use App\Services\RawgService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class VideojuegoController extends Controller
{
    /**
     * Función privada para guardar las series en mi base de datos con los datos extraidos de tmdb, esta la utilizaré solo cuando
     * el usuario cambie de estado, puntue o haga una reseña. 
     * Esto lo hago para no depender 100% de la API y qu emi base de datos no se sature ni tenga muchos datos sin sentido.
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
     * Función para traducir los géneros del select buscados de videojuegos
     */
    private function traducirGeneros($rawg)
    {
        $map = [
            'Action' => 'Acción',
            'Adventure' => 'Aventura',
            'RPG' => 'Rol',
            'Shooter' => 'Disparos',
            'Strategy' => 'Estrategia',
            'Simulation' => 'Simulación',
            'Sports' => 'Deportes',
            'Racing' => 'Carreras',
            'Indie' => 'Indie',
            'Puzzle' => 'Puzzle',
            'Arcade' => 'Arcade',
            'Platformer' => 'Plataformas',
            'Horror' => 'Terror',
            'Survival' => 'Supervivencia',
            'Massively Multiplayer' => 'Multijugador masivo',
            'Family' => 'Familiar',
            'Fighting' => 'Lucha',
            'Board Games' => 'Juegos de mesa',
            'Card' => 'Cartas',
            'Educational' => 'Educativo',
        ];

        $generos = collect($rawg->getGeneros()['results'] ?? [])
            ->map(function ($g) use ($map) {
                return [
                    'id' => $g['id'],
                    'name' => $map[$g['name']] ?? $g['name']
                ];
            })
            ->sortBy('name')
            ->values();

        return $generos;
    }

    /**
     * Función index, para mostrar el catálogo de las videojuegos
     */
    public function index(RawgService $rawg, Request $request)
    {
        
        $usuario = Auth::user();

        $listas = $usuario?->listas()->where('tipo', 'videojuego')->withCount('videojuegos')->get() ?? collect();
        $query = $request->query('q');
        $page = $request->query('page', 1);
        $data = $rawg->getJuegosBonitos($page);

        $results = collect($data['results'] ?? [])
            ->filter(fn($j) => !empty($j['background_image']))
            ->values()
            ->take(18);

        //Aquí la paginación la tuve que hacer diferente porque si no se rompía todo.
        $juegos = new LengthAwarePaginator(
            $results,
            $page * 18 + 18,
            18,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        $ocultas = ContenidoControl::where('tipo', 'videojuego')->where('visible', false)->pluck('api_id')->toArray();
        $videojuegosTotales = $juegos->reject(function ($videojuego) use ($ocultas) {
            return in_array($videojuego['id'], $ocultas);
        });

        //Traducir los generos en la función privada, si no sale todo en inglés.
        $generos = $this->traducirGeneros($rawg);
     
        return view('videojuego.index', compact('juegos', 'page', 'generos', 'listas'));
    }


    /**
     * Función para buscar un videojuego por su nombre
     */
    public function buscarVideojuego(RawgService $rawg, Request $request)
    {
        $usuario = Auth::user();

        $listas = $usuario?->listas()->where('tipo', 'videojuego')->withCount('videojuegos')->get() ?? collect();
        $query = $request->query('q');
        $page = $request->query('page', 1);
        $perPage = 18;

        //Si no hay búsqueda nos vamos al index
        if (!$query) {
            return redirect()->route('videojuegos.index');
        }

        //Videojuegos ocultos en mi base de datos
        $ocultos =ContenidoControl::where('tipo', 'videojuego')->where('visible', false)->pluck('api_id')->toArray();

        //Función de RAWG service
        $data = $rawg->buscarJuegos($query, $page);

        $results = collect($data['results'] ?? [])
            //Quitamos los que no tienen imagen
            ->filter(fn($j) => !empty($j['background_image']))

            //Quitamos los videojuegos ocultos
            ->reject(function ($juego) use ($ocultos) {
                return in_array($juego['id'], $ocultos);
            })

            ->values()
            ->take($perPage);

        //Paginación
        $total = ($page * $perPage) + $perPage;

        $juegos = new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        //Traducimos géneros
        $generos = $this->traducirGeneros($rawg);

        return view('videojuego.index', compact('juegos', 'query', 'generos', 'listas'));
    }

    /**
     * Función para filtrar videojuegos por género.
     */
    public function filtrarPorGenero(RawgService $rawg, Request $request, $generoId)
    {
        $usuario = Auth::user();

        $listas = $usuario?->listas()->where('tipo', 'videojuego')->withCount('videojuegos')->get() ?? collect();
        $query = $request->query('q');
        $page = $request->query('page', 1);
        $perPage = 18;

        //Videojuegos ocultos en mi base de dats
        $ocultos = \App\Models\ContenidoControl::where('tipo', 'videojuego')->where('visible', false)->pluck('api_id')->toArray();

        //Función de RAWG service
        $data = $rawg->getJuegosPorGenero($generoId, $page);

        $results = collect($data['results'] ?? [])
            //Quitamos los que no tienen imagen
            ->filter(fn($j) => !empty($j['background_image']))

            //Quitamos los videojuegos ocultos
            ->reject(function ($juego) use ($ocultos) {
                return in_array($juego['id'], $ocultos);
            })

            ->values()
            ->take($perPage);

        //Paginación
        $total = ($page * $perPage) + $perPage;

        $juegos = new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path' => route('videojuegos_genero', $generoId),
                'query' => $request->query()
            ]
        );

        //Traducimos géneros
        $generos = $this->traducirGeneros($rawg);

        return view('videojuego.index', compact('juegos', 'generoId', 'query', 'generos', 'listas'));
    }

    /**
     * Función donde vemos los detalles de los videojuegos y las reseñas también.
     */
    public function verDetallesJuego($id, RawgService $rawgService)
    {
        $usuario = Auth::user();

        $juegoBD = Videojuego::where('rawg_id', $id)->first();

        $juego = $rawgService->getJuego($id);
        if (!empty($juego['released'])) {
            $juego['released'] = Carbon::parse($juego['released'])->format('d/m/Y');
        }
        $juego['rating'] = number_format($juego['rating'] ?? 0, 1);

        //Plataformas
        $plataformas = collect($juego['platforms'] ?? [])
            ->take(5)
            ->map(function ($item) {
                return [
                    'name' => $item['platform']['name'] ?? '',
                    'slug' => $item['platform']['slug'] ?? '',
                ];
            });

        //Estado del videojuego
        $estado = null;
        $puntuacion = null;
        $resenas = collect();

        if ($usuario && $juegoBD) {

            $relacion = $usuario->videojuegos()->where('videojuego_id', $juegoBD->id)->first();
            if ($relacion) {
                $estado = $relacion->pivot->estado;
                $puntuacion = $relacion->pivot->puntuacion;
            }
            $resenas = ResenaVideojuego::where('videojuego_id', $juegoBD->id)
                ->where('aprobada', true)
                ->with('usuario')
                ->latest()
                ->get();
        }

        $oculta = ContenidoControl::where('tipo', 'videojuego')
            ->where('api_id', $id)
            ->where('visible', false)
            ->exists();

        if ($oculta) {
            if (!$usuario) {
                abort(403);
            }
            if (!in_array($usuario->role, ['gestor', 'admin'])) {
                abort(403);
            }

        }

        //Listas
        $listas = $usuario?->listas()
            ->where('tipo', 'videojuego')
            ->withCount('videojuegos')
            ->get() ?? collect();

        return view('videojuego.detalle-videojuego', compact('juego','plataformas','estado','puntuacion','juegoBD','resenas','listas', 'oculta'));
    }

    /**
     * Función para marcar el estado de los videojuegos. Aqui es donde guardamos el videojuego en nuestra base de datos
    */ 
    public function guardarEstadoVideojuego(Request $request)
    {
        $usuario = Auth::user();
        if (!$usuario) return redirect()->route('login');

        $request->validate([
            'videojuego_id' => 'required',
            'estado' => 'required|in:pendiente,jugado,favorito'
        ]);

        $videojuego = $this->guardarJuegoDesdeRawg($request->videojuego_id);
        $usuario->videojuegos()->syncWithoutDetaching([
            $videojuego->id => [
                'estado' => $request->estado,
                'puntuacion' => null
            ]
        ]);

        return back()->with('mensaje', 'Estado guardado');
    }

    /**
     * Función para guardar la puntuación que pone el usuario, aquí también hacemos que se guarde la película en la base de datos
     */
    public function guardarRatingVideojuego(Request $request)
    {
        $usuario = Auth::user();
        if (!$usuario) return redirect()->route('login');

        $request->validate([
            'videojuego_id' => 'required',
            'puntuacion' => 'required|integer|min:1|max:5'
        ]);

        // Guardar el videojuegp
        $videojuego = $this->guardarJuegoDesdeRawg($request->videojuego_id);
        // Obtener pivot actual
        $pivot = $usuario->videojuegos()->where('videojuego_id', $videojuego->id)->first()?->pivot;
        // Guardar rating sin perder estado
        $usuario->videojuegos()->syncWithoutDetaching([
            $videojuego->id => [
                'puntuacion' => $request->puntuacion,
                'estado' => $pivot->estado ?? 'jugado' 
            ]
        ]);

        return back()->with('mensaje', 'Puntuación guardada');
    }

    /**
     * Función para guardar un videojuego en una lista personalizada por el usuario. Aquí también guardamoslos datos en la base de datos
     */
    public function guardarVideojuegoEnLista(Request $request)
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $request->validate([
            'videojuego_id' => 'required',
            'lista_id' => 'required'
        ]);

        $videojuego = $this->guardarJuegoDesdeRawg($request->videojuego_id);
        if (in_array($request->lista_id, ['pendiente', 'jugado', 'favorito'])) {
            $pivotActual = $usuario->videojuegos()->where('videojuego_id', $videojuego->id)->first()?->pivot;
            $usuario->videojuegos()->syncWithoutDetaching([
                $videojuego->id => [
                    'estado' => $request->lista_id,
                    'puntuacion' => $pivotActual->puntuacion ?? null
                ]
            ]);

        } else {
            $lista = $usuario->listas()->where('tipo', 'videojuego')->where('id', $request->lista_id)->firstOrFail();
            $lista->videojuegos()->syncWithoutDetaching([
                $videojuego->id
            ]);
        }

        return redirect()->back()->with('mensaje', 'Videojuego añadido correctamente');
    }

    public function ocultarVideojuego(Request $request)
    {
        ContenidoControl::updateOrCreate(
            [
                'api_id' => $request->videojuego_id,
                'tipo' => 'videojuego'
            ],
            [
                'visible' => false
            ]
        );

        return back()->with('mensaje', 'Videojuego oculto correctamente');
    }

    public function mostrarVideojuego(Request $request)
    {
        ContenidoControl::updateOrCreate(
            [
                'api_id' => $request->videojuego_id,
                'tipo' => 'videojuego'
            ],
            [
                'visible' => true
            ]
        );

        return back()->with('mensaje', 'Videojuego visible correctamente');
    }
}