<?php

namespace App\Http\Controllers;

use App\Models\ContenidoControl;
use App\Models\Genero;
use App\Models\Pelicula;
use App\Models\ResenaPelicula;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PeliculaController extends Controller
{
    /**
     * Función privada para guardar las películas en mi base de datos con los datos extraidos de tmdb, esta la utilizaré solo cuando
     * el usuario cambie de estado, puntue o haga una reseña. 
     * Esto lo hago para no depender 100% de la API y qu emi base de datos no se sature ni tenga muchos datos sin sentido.
     */
    private function guardarPeliculaDesdeTmdb($tmdbId)
    {
        $data = cache()->remember("pelicula_{$tmdbId}", 3600, function () use ($tmdbId) {
            return Http::withToken(config('services.tmdb.token'))
                ->get("https://api.themoviedb.org/3/movie/" . $tmdbId)
                ->json();
        });

        $pelicula = Pelicula::updateOrCreate(
            ['tmdb_id' => $tmdbId],
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

        //Como en esta API tiene los géneros a parte, lo que hago es guardarlo aquí también para ir obteniendolos.
    
        if (isset($data['genres'])) {
            $idsGeneros = [];
            foreach ($data['genres'] as $genre) {
                $genero = Genero::firstOrCreate([
                    'nombre' => $genre['name']
                ]);
                $idsGeneros[] = $genero->id;
            }
            //Esto añade generos nuevos sin eliminar los anteriores, porque tuve problemas con eso.
            $pelicula->generos()->syncWithoutDetaching($idsGeneros);
        }

        return $pelicula;
    }

    /**
     * Función index, para mostrar el catálogo de las películas
     */
    public function index(TmdbService $tmdb, Request $request)
    {
        $usuario = Auth::user();
        $listas = $usuario?->listas()->where('tipo', 'pelicula')->withCount('peliculas')->get() ?? collect();
        $ocultas = ContenidoControl::where('tipo', 'pelicula')->where('visible', false)->pluck('api_id')->toArray();
        
        //Para tener la paginación de las películas a mi gusto
        $page = $request->query('page', 1);
        $porPagina = 18;
        $tmdbPorPagina = 20;
        $totalNecesario = $page * $porPagina;
        $paginasTmdb = ceil($totalNecesario / $tmdbPorPagina);

        $peliculasTotales = collect();

        for ($i = 1; $i <= $paginasTmdb; $i++) {
            //Saco la función que he creado en tmdb por services.
            $data = $tmdb->getPeliculasPopulares($i);
            $peliculasTotales = $peliculasTotales->merge($data['results'] ?? []);
        }

        $peliculasTotales = $peliculasTotales->reject(function ($pelicula) use ($ocultas) {
            return in_array($pelicula['id'], $ocultas);
        });
        //La única función de laravel que me ha dejado paginar como quería y sin que me salieran 500 páginas vacías algunas.
        $peliculas = new LengthAwarePaginator(
            $peliculasTotales->forPage($page, $porPagina),
            $peliculasTotales->count(),
            $porPagina,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return view('pelicula.index', compact('peliculas', 'listas'));
    }

    /**
     * Función para buscar una película por su nombre
     */
    public function buscarPelicula(TmdbService $tmdb, Request $request)
    {
        $usuario = Auth::user();
        $listas = $usuario?->listas()->where('tipo', 'pelicula')->withCount('peliculas')->get() ?? collect();

        //Paginación
        $query = $request->query('q');
        $page = $request->query('page', 1);
        $porPagina = 18;
        
        //Si no hay películas se va al index
        if (!$query) {
            return redirect()->route('inicio_peliculas');
        }

        //Traemos las películas ocultas en BD
        $ocultas = ContenidoControl::where('tipo', 'pelicula')->where('visible', false)->pluck('api_id')->toArray();

        //Función del services
        $data = $tmdb->buscarPeliculas($query, $page);

        //Transformamos resultados de la API
        $peliculasApi = collect($data['results'] ?? [])
            ->reject(function ($pelicula) use ($ocultas) {
                return in_array($pelicula['id'], $ocultas);
            });

        $peliculas = new LengthAwarePaginator(
            $peliculasApi->forPage(1, $porPagina),
            $data['total_results'] ?? $peliculasApi->count(),
            $porPagina,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return view('pelicula.index', compact('peliculas', 'query', 'listas'));
    }

    /**
     * Función para filtrar películas por género.
     */
    public function filtrarPorGenero(TmdbService $tmdb, Request $request, $genero)
    {
        $usuario = Auth::user();
        $listas = $usuario?->listas()->where('tipo', 'pelicula')->withCount('peliculas')->get() ?? collect();

        $page = $request->query('page', 1);
        $porPagina = 18;
        $tmdbPorPagina = 20;
        $totalNecesario = $page * $porPagina;
        $paginasTmdb = ceil($totalNecesario / $tmdbPorPagina);
        $peliculasTotales = collect();

        //Películas ocultas en mi base de datos
        $ocultas = ContenidoControl::where('tipo', 'pelicula')->where('visible', false)->pluck('api_id')->toArray();

        for ($i = 1; $i <= $paginasTmdb; $i++) {
            //Función de services
            $data = $tmdb->getPeliculasPorGenero($genero, $i);
            $peliculasTotales = $peliculasTotales->merge($data['results'] ?? []);
        }

        //Filtrar películas ocultas
        $peliculasTotales = $peliculasTotales->reject(function ($pelicula) use ($ocultas) {
            return in_array($pelicula['id'], $ocultas);
        });

        $peliculas = new LengthAwarePaginator(
            $peliculasTotales->forPage($page, $porPagina),
            $peliculasTotales->count(),
            $porPagina,
            $page,
            [
                'path' => route('peliculas_genero', $genero),
                'query' => $request->query()
            ]
        );

        return view('pelicula.index', compact('peliculas', 'genero', 'listas'));
    }

    /**
     * Función donde vemos los detalles de las películas y las reseñas también.
     */
    public function verDetallesPelicula($id)
    {
        $usuario = Auth::user();

        $listas = $usuario?->listas()->where('tipo', 'pelicula')->withCount('peliculas')->get() ?? collect();
        //petición a la API
        $response = Http::withToken(config('services.tmdb.token'))->get("https://api.themoviedb.org/3/movie/$id");
        $pelicula = $response->json();

        //Formatear la fecha
        if (!empty($pelicula['release_date'])) {
            $pelicula['release_date'] = Carbon::parse($pelicula['release_date'])
                ->format('d/m/Y');
        }
        //Poner solo un decimal
        $pelicula['vote_average'] = number_format($pelicula['vote_average'] ?? 0, 1);
        //petición de los actores de la pelicula
        $creditos = Http::withToken(config('services.tmdb.token'))->get("https://api.themoviedb.org/3/movie/$id/credits");
        //Solo muestro 5
        $actores = array_slice($creditos->json()['cast'] ?? [], 0, 5);

        //Busca en la base de datos si existe para poder devolver la puntuación y el estado si lo hay
        $peliculaBD = Pelicula::where('tmdb_id', $id)->first();
        $estado = null;
        $puntuacion = null;

        if ($usuario && $peliculaBD) {
            $relacion = $usuario->peliculas()->withPivot('estado', 'puntuacion')->where('pelicula_id', $peliculaBD->id)->first();
            if ($relacion) {
                $estado = $relacion->pivot->estado;
                $puntuacion = $relacion->pivot->puntuacion;
            }
        }

        // Sacamos los generos si están en la base de datos-
        $generosBD = [];

        if ($peliculaBD) {
            $generosBD = $peliculaBD->generos()->pluck('nombre');
        }

        //Sacamos las reseñas que esten aprobadas por el gestor.
        $resenas = collect();
        if ($peliculaBD) {
            $resenas = ResenaPelicula::where('pelicula_id', $peliculaBD->id)
            ->where('aprobada', true)
                ->with('usuario')
                ->latest()
                ->get();
        }

        //Para que el gestor pueda ocultar el contenido si lo ve necesario
        $oculta = ContenidoControl::where('tipo', 'pelicula')
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
        // dd([
        //     'oculta' => $oculta,
        //     'usuario' => $usuario,
        //     'role' => $usuario?->role,
        //     'check' => in_array($usuario->role, ['gestor', 'admin'])
        // ]);

        return view('pelicula.detalle-pelicula', compact('pelicula', 'actores', 'estado', 'puntuacion', 'peliculaBD', 'generosBD', 'resenas', 'listas', 'oculta'));
    }

    /**
     * Función para marcar el estado de la pelicula. Aqui es donde guardamos la pelicula en nuestra base de datos
    */ 
    public function guardarEstadoPelicula(Request $request)
    {
        $usuario = Auth::user();
        if (!$usuario) return redirect()->route('login');
        //Validar los datos
        $request->validate([
            'pelicula_id' => 'required',
            'estado' => 'required|in:pendiente,vista,favorito'
        ]);

        //Guardamos la película en la base de datos
        $pelicula = $this->guardarPeliculaDesdeTmdb($request->pelicula_id);

        $pivot = $usuario->peliculas()->where('pelicula_id', $pelicula->id)->first()?->pivot;
        $usuario->peliculas()->syncWithoutDetaching([
            $pelicula->id => [
                'estado' => $request->estado,
                'puntuacion' => $pivot->puntuacion ?? null
            ]
        ]);

        return back()->with('mensaje', 'Estado actualizado');
    }

    /**
     * Función para guardar la puntuación que pone el usuario, aquí también hacemos que se guarde la película en la base de datos
     */
    public function guardarRating(Request $request)
    {
        $usuario = Auth::user();
        if (!$usuario) return redirect()->route('login');
        //Validación de datos
        $request->validate([
            'pelicula_id' => 'required',
            'puntuacion' => 'required|integer|min:1|max:5'
        ]);

        $pelicula = $this->guardarPeliculaDesdeTmdb($request->pelicula_id);
        $pivot = $usuario->peliculas()->where('pelicula_id', $pelicula->id)->first()?->pivot;

        //Si el usuario lo puntúa hacemosque automaticamente cambie el estado a vista
        $usuario->peliculas()->syncWithoutDetaching([
            $pelicula->id => [
                'puntuacion' => $request->puntuacion,
                'estado' => $pivot->estado ?? 'vista'
            ]
        ]);

        return back()->with('mensaje', 'Puntuación guardada');
    }

    /**
     * Función para guardar una película en una lista personalizada por el usuario. Aquí también guardamoslos datos en la base de datos
     */
    public function guardarEnLista(Request $request)
    {
        $usuario = Auth::user();
        if (!$usuario) return redirect()->route('login');

        $request->validate([
            'pelicula_id' => 'required',
            'lista_id' => 'required'
        ]);

        $pelicula = $this->guardarPeliculaDesdeTmdb($request->pelicula_id);

        if (in_array($request->lista_id, ['pendiente', 'vista', 'favorito'])) {

            $pivot = $usuario->peliculas()->where('pelicula_id', $pelicula->id)->first()?->pivot;
            $usuario->peliculas()->syncWithoutDetaching([
                $pelicula->id => [
                    'estado' => $request->lista_id,
                    'puntuacion' => $pivot->puntuacion ?? null
                ]
            ]);

        } else {

            $lista = $usuario->listas()->where('tipo', 'pelicula')->findOrFail($request->lista_id);
            $lista->peliculas()->syncWithoutDetaching([
                $pelicula->id
            ]);
        }

        return back()->with('mensaje', 'Añadido correctamente');
    }

    //Función para que el gestor pueda ocultar el contenido
    public function ocultarPelicula(Request $request)
    {
        $request->validate([
            'pelicula_id' => 'required'
        ]);

        ContenidoControl::updateOrCreate(
            [
                'tipo' => 'pelicula',
                'api_id' => $request->pelicula_id,
            ],
            [
                'visible' => false
            ]
        );

        return back()->with('mensaje', 'Película oculta correctamente');
    }

    //Función para volver a mostrar la película que hemos oculatado
    public function mostrarPelicula(Request $request)
    {
        ContenidoControl::where('tipo', 'pelicula')
            ->where('api_id', $request->pelicula_id)
            ->update(['visible' => true]);

        return back()->with('mensaje', 'Película visible correctamente');;
    }

}

