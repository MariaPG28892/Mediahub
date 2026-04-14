<?php

namespace App\Http\Controllers;

use App\Models\ContenidoControl;
use App\Models\ResenaPelicula;
use App\Models\ResenaSerie;
use App\Models\ResenaVideojuego;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GestorController extends Controller
{
    /**
     * Función para sacar los datos en el panel de gestor, aquí pongo un análisis de alguna de las estadísticas 
     * que encontramos en nuestra página tanto de usuarios como de las reseñas.
     */
    public function index()
    {
        $usuariosActivos = User::where('ultimo_login', '>=', Carbon::now()->subDays(7))->count();
        $totalUsuarios = User::count();

        $peliculasPendientes = ResenaPelicula::where('aprobada', false)->count();
        $seriesPendientes = ResenaSerie::where('aprobada', false)->count();
        $videojuegosPendientes = ResenaVideojuego::where('aprobada', false)->count();
        $totalPendientes = $peliculasPendientes + $seriesPendientes + $videojuegosPendientes;

        $ultimasPeliculas = ResenaPelicula::with(['usuario', 'pelicula'])
            ->where('aprobada', false)
            ->latest()
            ->take(3)
            ->get();

        $ultimasSeries = ResenaSerie::with(['usuario', 'serie'])
            ->where('aprobada', false)
            ->latest()
            ->take(3)
            ->get();

        $ultimasVideojuegos = ResenaVideojuego::with(['usuario', 'videojuego'])
            ->where('aprobada', false)
            ->latest()
            ->take(3)
            ->get();

        return view('gestor.index', compact('usuariosActivos', 'totalUsuarios', 'peliculasPendientes', 'seriesPendientes', 'videojuegosPendientes', 'totalPendientes', 'ultimasPeliculas', 'ultimasSeries', 'ultimasVideojuegos'));
    }

    /**
     * Función para buscar las reseñas de las reseñas pendientes de cada una de las categorías.
     */
    public function resenasPendientes()
    {
        $resenas = collect()
            ->merge($this->getPendientes(ResenaPelicula::class, ['pelicula', 'usuario']))
            ->merge($this->getPendientes(ResenaSerie::class, ['serie', 'usuario']))
            ->merge($this->getPendientes(ResenaVideojuego::class, ['videojuego', 'usuario']))
            ->sortByDesc('created_at');

        return view('gestor.gestionar_resenas', compact('resenas'));
    }

    //Función privada para sacar las 
    private function getPendientes($model, $relations)
    {
        return $model::with($relations)
            ->where('aprobada', false)
            ->latest()
            ->get();
    }

    //Función para sacar las reseñas de peliculas en su vista correspondiente
    public function peliculas()
    {
        $resenas = ResenaPelicula::with(['usuario', 'pelicula'])->where('aprobada', false)->latest()->get();

        return view('gestor.gestionar_resenas_peliculas', compact('resenas'));
    }

    //Función para sacar las reseñas de series en su vista correspondiente
    public function series()
    {
        $resenas = ResenaSerie::with(['usuario', 'serie'])->where('aprobada', false)->latest()->get();

        return view('gestor.gestionar_resenas_series', compact('resenas'));
    }

    ////Función para sacar las reseñas de videojegos en su vista correspondiente
    public function videojuegos()
    {
        $resenas = ResenaVideojuego::with(['usuario', 'videojuego'])->where('aprobada', false)->latest()->get();

        return view('gestor.gestionar_resenas_videojuegos', compact('resenas'));
    }

    //Función para aprobar reseñas 
    public function aprobar($id)
    {
        $resena = $this->buscarResena($id);
        if (!$resena){
            return back();
        } 

        $resena->update(['aprobada' => 1]);

        return back()->with('success', 'Reseña aprobada');
    }

    /**
     * Función para eliminar las reseñas que el gestor considere necesaia
     */
    public function eliminar($id)
    {
        $resena = $this->buscarResena($id);

        if (!$resena){
            return back();
        } 
        $resena->delete();

        return back()->with('success', 'Reseña rechazada');
    }

    /**
     * Función para buscar la reseña con el id que nos trae del formulario
     */
    private function buscarResena($id)
    {
        return ResenaPelicula::find($id) ?? ResenaSerie::find($id) ?? ResenaVideojuego::find($id);
    }

    /**
     * Función para bloquear un usuario, pueden hacerlo tanto el gestor como el administrador.
     */
    public function bloquearUsuario($id)
    {
        $user = User::findOrFail($id);

        //Si es un gestor no puede bloquear a un administrador 
        if ($user->role === 'admin') {
            return back()->with('error', 'No puedes bloquear a un administrador');
        }
        $user->bloqueado = true;
        $user->save();

        return back()->with('success', 'Usuario bloqueado');
    }

    /**
     * Función para sacar los usuarios y listarlos en las vistas del gestor donde sea necesario.
     */
    public function usuarios()
    {
        $usuarios = User::latest()->get();

        return view('gestor.usuarios', compact('usuarios'));
    }

    /**
     * Función para desbloquear un usuario. Pueden hacerlo tanto admin como gestor.
     */
    public function desbloquearUsuario($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'No puedes modificar un administrador');
        }
        $user->bloqueado = false;
        $user->save();

        return back()->with('success', 'Usuario desbloqueado');
    }

    /**
     * Función para ocultar contenido que exportamos de la API y no queremos que este en nuestra app, y podamos tanto 
     * ocultarlo como volver a mostrarlo.
     */
    public function contenidoOculto()
    {
        $ocultos = ContenidoControl::where('visible', false)->orderBy('tipo')->latest()->get();

        foreach ($ocultos as $item) {

            //Películas
            if ($item->tipo === 'pelicula') {

                $data = cache()->remember("pelicula_oculta_{$item->api_id}", 3600, function () use ($item) {
                    return Http::withToken(config('services.tmdb.token'))
                        ->get("https://api.themoviedb.org/3/movie/{$item->api_id}")
                        ->json();
                });

                $item->titulo = $data['title'] ?? 'Película';
                $item->imagen = isset($data['poster_path']) ? "https://image.tmdb.org/t/p/w300{$data['poster_path']}" : Storage::url('default.png');
            }

            //Series
            if ($item->tipo === 'serie') {

                $data = cache()->remember("serie_oculta_{$item->api_id}", 3600, function () use ($item) {
                    return Http::withToken(config('services.tmdb.token'))
                        ->get("https://api.themoviedb.org/3/tv/{$item->api_id}")
                        ->json();
                });

                $item->titulo = $data['name'] ?? 'Serie';
                $item->imagen = isset($data['poster_path']) ? "https://image.tmdb.org/t/p/w300{$data['poster_path']}" : Storage::url('default.png');
            }

            //Videojuegos
            if ($item->tipo === 'videojuego') {

                $data = cache()->remember("juego_oculto_{$item->api_id}", 3600, function () use ($item) {
                    return Http::get("https://api.rawg.io/api/games/{$item->api_id}", [
                        'key' => config('services.rawg.key')
                    ])->json();
                });

                $item->titulo = $data['name'] ?? 'Videojuego';
                $item->imagen = $data['background_image'] ?? Storage::url('default.png');
            }
        }

        return view('gestor.contenido_oculto', compact('ocultos'));
    }

    public function mostrarContenido(Request $request)
    {
        ContenidoControl::where('api_id', $request->id)
            ->where('tipo', $request->tipo)
            ->update(['visible' => true]);

        return back()->with('success', 'Contenido mostrado correctamente');
    }

}