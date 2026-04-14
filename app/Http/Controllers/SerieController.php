<?php

namespace App\Http\Controllers;

use App\Models\ContenidoControl;
use App\Models\ResenaSerie;
use App\Models\Serie;
use App\Models\Genero;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class SerieController extends Controller
{
    /**
     * Función privada para guardar las series en mi base de datos con los datos extraidos de tmdb, esta la utilizaré solo cuando
     * el usuario cambie de estado, puntue o haga una reseña. 
     * Esto lo hago para no depender 100% de la API y qu emi base de datos no se sature ni tenga muchos datos sin sentido.
     */
    private function guardarSerieDesdeTmdb($tmdbId)
    {
        $data = cache()->remember("serie_{$tmdbId}", 3600, function () use ($tmdbId) {
            return Http::withToken(config('services.tmdb.token'))
                ->get("https://api.themoviedb.org/3/tv/$tmdbId")
                ->json();
        });

        $serie = Serie::updateOrCreate(
            ['tmdb_id' => $tmdbId],
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

        //Generos
        $ids = [];
        foreach ($data['genres'] ?? [] as $genre) {
            $genero = Genero::firstOrCreate([
                'nombre' => $genre['name']
            ]);
            $ids[] = $genero->id;
        }
        if (!empty($ids)) {
            $serie->generos()->sync($ids);
        }
        return $serie;
    }

    /**
     * Función index, para mostrar el catálogo de las series
     */
    public function index(TmdbService $tmdb, Request $request)
    {
        $usuario = Auth::user();
        $listas = $usuario?->listas()->where('tipo', 'serie')->withCount('series')->get() ?? collect();

        //Paginación
        $page = $request->query('page', 1);
        $porPagina = 18;
        $tmdbPorPagina = 20;
        $totalNecesario = $page * $porPagina;
        $paginasTmdb = ceil($totalNecesario / $tmdbPorPagina);
        $seriesTotales = collect();
        $ocultas = ContenidoControl::where('tipo', 'serie')->where('visible', false)->pluck('api_id')->toArray();

        for ($i = 1; $i <= $paginasTmdb; $i++) {
            $data = $tmdb->getSeriesPopulares($i);
            $seriesTotales = $seriesTotales->merge($data['results'] ?? []);
        }

        $seriesTotales = $seriesTotales->reject(function ($serie) use ($ocultas) {
            return in_array($serie['id'], $ocultas);
        });

        $series = new LengthAwarePaginator(
            $seriesTotales->forPage($page, $porPagina),
            $seriesTotales->count(),
            $porPagina,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return view('serie.index', compact('series', 'listas'));
    }

    /**
     * Función para buscar una serie por su nombre
     */
    public function buscarSerie(TmdbService $tmdb, Request $request)
    {
        $usuario = Auth::user();

        $listas = $usuario?->listas()
            ->where('tipo', 'serie')
            ->withCount('series')
            ->get() ?? collect();

        $query = $request->query('q');
        $page = $request->query('page', 1);
        $porPagina = 18;

        if (!$query) {
            return redirect()->route('inicio_series');
        }

        $data = $tmdb->buscarSeries($query, $page);
        $seriesApi = collect($data['results'] ?? []);

        $ocultas = ContenidoControl::where('tipo', 'serie')->where('visible', false)->pluck('api_id')->toArray();
        $data = $tmdb->buscarSeries($query, $page);
        $seriesApi = collect($data['results'] ?? [])
            ->reject(function ($serie) use ($ocultas) {
                return in_array($serie['id'], $ocultas);
        });

        $series = new LengthAwarePaginator(
            $seriesApi->forPage(1, $porPagina),
            $data['total_results'] ?? $seriesApi->count(),
            $porPagina,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return view('serie.index', compact('series', 'query', 'listas'));
    }

    /**
     * Función para filtrar series por género.
     */
    public function filtrarPorGenero(TmdbService $tmdb, Request $request, $genero)
    {
        $usuario = Auth::user();

        $listas = $usuario?->listas()
            ->where('tipo', 'serie')
            ->withCount('series')
            ->get() ?? collect();

        $page = $request->query('page', 1);
        $porPagina = 18;
        $tmdbPorPagina = 20;
        $totalNecesario = $page * $porPagina;
        $paginasTmdb = ceil($totalNecesario / $tmdbPorPagina);
        $seriesTotales = collect();

        for ($i = 1; $i <= $paginasTmdb; $i++) {
            $data = $tmdb->getSeriesPorGenero($genero, $i);
            $seriesTotales = $seriesTotales->merge($data['results'] ?? []);
        }
        $ocultas = \App\Models\ContenidoControl::where('tipo', 'serie')->where('visible', false)->pluck('api_id')->toArray();
        $seriesTotales = $seriesTotales->reject(function ($serie) use ($ocultas) {
            return in_array($serie['id'], $ocultas);
        });

        $series = new LengthAwarePaginator(
            $seriesTotales->forPage($page, $porPagina),
            $seriesTotales->count(),
            $porPagina,
            $page,
            [
                'path' => route('series_genero', $genero),
                'query' => $request->query()
            ]
        );

        return view('serie.index', compact('series', 'genero', 'listas'));
    }

    /**
     * Función donde vemos los detalles de las series y las reseñas también.
     */
    public function verDetallesSerie($id)
    {
        $usuario = Auth::user();

        $listas = $usuario?->listas()->where('tipo', 'serie')->withCount('series')->get() ?? collect();
        $response = Http::withToken(config('services.tmdb.token'))->get("https://api.themoviedb.org/3/tv/$id");
        $serie = $response->json();

        if (!empty($serie['first_air_date'])) {
            $serie['first_air_date'] = Carbon::parse($serie['first_air_date'])
                ->format('d/m/Y');
        }

        $serie['vote_average'] = number_format($serie['vote_average'] ?? 0, 1);
        //actores
        $creditos = Http::withToken(config('services.tmdb.token'))->get("https://api.themoviedb.org/3/tv/$id/credits");
        $actores = array_slice($creditos->json()['cast'] ?? [], 0, 5);
        //estado de la serie
        $serieBD = Serie::where('tmdb_id', $id)->first();
        $estado = null;
        $puntuacion = null;

        if ($usuario && $serieBD) {
            $relacion = $usuario->series()
                ->withPivot('estado', 'puntuacion')
                ->where('serie_id', $serieBD->id)
                ->first();

            if ($relacion) {
                $estado = $relacion->pivot->estado;
                $puntuacion = $relacion->pivot->puntuacion;
            }
        }
        //Generos
        $generosBD = [];
        if ($serieBD) {
            $generosBD = $serieBD->generos()
                ->pluck('nombre');
        }
        $resenas = collect();
        //reseñas
        if ($serieBD) {
            $resenas = ResenaSerie::where('serie_id', $serieBD->id)
                ->where('aprobada', true)
                ->with('usuario')
                ->latest()
                ->get();
        }

        $oculta = ContenidoControl::where('tipo', 'serie')
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

        return view('serie.detalle-serie', compact('serie','actores','estado','puntuacion','serieBD','generosBD','resenas','listas', 'oculta'));
    }

     /**
     * Función para marcar el estado de la serie. Aqui es donde guardamos la serie en nuestra base de datos
    */ 
    public function guardarEstadoSerie(Request $request)
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $request->validate([
            'serie_id' => 'required',
            'estado' => 'required|in:pendiente,vista,favorito'
        ]);

        $serie = $this->guardarSerieDesdeTmdb($request->serie_id);

        $pivotActual = $usuario->series()
            ->where('serie_id', $serie->id)
            ->first()?->pivot;

        $usuario->series()->syncWithoutDetaching([
            $serie->id => [
                'estado' => $request->estado,
                'puntuacion' => $pivotActual->puntuacion ?? null
            ]
        ]);

        return redirect()
            ->route('ver_detalle_serie', $request->serie_id)
            ->with('mensaje', 'Serie guardada correctamente');
    }

    /**
     * Función para guardar la puntuación que pone el usuario, aquí también hacemos que se guarde la película en la base de datos
     */
    public function guardarRatingSerie(Request $request)
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $request->validate([
            'serie_id' => 'required',
            'puntuacion' => 'required|integer|min:1|max:5'
        ]);

        $serie = $this->guardarSerieDesdeTmdb($request->serie_id);
        $pivotActual = $usuario->series()->where('serie_id', $serie->id)->first()?->pivot;
        $usuario->series()->syncWithoutDetaching([
            $serie->id => [
                'puntuacion' => $request->puntuacion,
                'estado' => $pivotActual->estado ?? 'vista'
            ]
        ]);

        return redirect()->route('ver_detalle_serie', $request->serie_id)->with('mensaje', 'Puntuación guardada correctamente');
    }

    /**
     * Función para guardar una película en una lista personalizada por el usuario. Aquí también guardamoslos datos en la base de datos
     */
    public function guardarSerieEnLista(Request $request)
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $request->validate([
            'serie_id' => 'required',
            'lista_id' => 'required'
        ]);

        $serie = $this->guardarSerieDesdeTmdb($request->serie_id);

        if (in_array($request->lista_id, ['pendiente', 'vista', 'favorito'])) {

            $pivotActual = $usuario->series()->where('serie_id', $serie->id)->first()?->pivot;
            $usuario->series()->syncWithoutDetaching([
                $serie->id => [
                    'estado' => $request->lista_id,
                    'puntuacion' => $pivotActual->puntuacion ?? null
                ]
            ]);

        } else {

            $lista = $usuario->listas()->where('tipo', 'serie')->findOrFail($request->lista_id);
            $lista->series()->syncWithoutDetaching([
                $serie->id
            ]);
        }

        return redirect()->back()->with('mensaje', 'Serie añadida correctamente');
    }

    public function ocultarSerie(Request $request)
    {
        ContenidoControl::updateOrCreate(
            [
                'api_id' => $request->serie_id,
                'tipo' => 'serie'
            ],
            [
                'visible' => false
            ]
        );

        return back()->with('mensaje', 'Serie oculta correctamente');
    }

    public function mostrarSerie(Request $request)
    {
        ContenidoControl::updateOrCreate(
            [
                'api_id' => $request->serie_id,
                'tipo' => 'serie'
            ],
            [
                'visible' => true
            ]
        );

        return back()->with('mensaje', 'Serie visible correctamente');
    }

}