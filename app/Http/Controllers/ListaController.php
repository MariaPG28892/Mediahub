<?php

namespace App\Http\Controllers;

use App\Models\Lista;
use App\Models\Pelicula;
use App\Models\Serie;
use App\Models\Videojuego;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListaController extends Controller
{
    /**
     * Función privada que he hecho para aplicar los filtros necesarios y poder usar las mismas funciones de crear lista y eliminar lista
     * ahorrando duplicación de código.
     */
    private function aplicarFiltros($query, $tipo)
    {
        if ($tipo === 'pendientes') {
            return $query->wherePivot('estado', 'pendiente');
        }

        if ($tipo === 'vistas' || $tipo === 'jugados') {
            return $query->wherePivot('estado', 'vista');
        }

        if ($tipo === 'favoritos') {
            return $query->wherePivot('estado', 'favorito');
        }

        if ($tipo === 'puntuadas') {
            return $query->whereNotNull('puntuacion')
                         ->orderByDesc('puntuacion');
        }

        return $query;
    }

    /**
     * Index de las listas de las películas para sacarlas con estado, puntuación y listas creadas por el usuario.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tipo = $request->input('tipo', 'pendientes');
        $listas = $user->listas()->where('tipo', 'pelicula')->withCount('peliculas')->get();

        $listaSeleccionada = null;
        if (is_numeric($tipo)) {
            $listaSeleccionada = Lista::where('id', $tipo)->where('user_id', $user->id)->first();
        }

        $query = $user->peliculas()->withPivot('estado', 'puntuacion');
        $query = $this->aplicarFiltros($query, $tipo);
        if (is_numeric($tipo)) {
            $query = Pelicula::whereHas('listas', function ($q) use ($tipo, $user) {
                $q->where('listas.id', $tipo)
                ->where('listas.user_id', $user->id);
            });
        }

        // Para que solo pagine 27 por página
        $peliculas = $query->paginate(27);

        return view('lista.index', compact('listas', 'peliculas', 'tipo', 'listaSeleccionada'));
    }

    /**
     * Index para sacar las series con estado, puntuación y listas creadas por el usuario
     */
    public function indexSeries(Request $request)
    {
        $user = Auth::user();
        $tipo = $request->input('tipo', 'pendientes');
        $listas = $user->listas()->where('tipo', 'serie')->withCount('series')->get();

        $listaSeleccionada = null;
        if (is_numeric($tipo)) {
            $listaSeleccionada = Lista::where('id', $tipo)->where('user_id', $user->id)->first();
        }

        $query = $user->series()->withPivot('estado', 'puntuacion');
        $query = $this->aplicarFiltros($query, $tipo);
        if (is_numeric($tipo)) {
            $query = Serie::whereHas('listas', function ($q) use ($tipo, $user) {
                $q->where('listas.id', $tipo)->where('listas.user_id', $user->id);
            });
        }

        $series = $query->paginate(27);

        return view('lista.series', compact('listas', 'series', 'tipo', 'listaSeleccionada'));
    }

    /**
     *Index de videojuegos, para sacar los videojuegos por estado, puntuación y listas creadas por el usuario
     */
    public function indexVideojuegos(Request $request)
    {
        $user = Auth::user();
        $tipo = $request->input('tipo', 'pendientes');
        $listas = $user->listas()->where('tipo', 'videojuego')->withCount('videojuegos')->get();

        $listaSeleccionada = null;
        if (is_numeric($tipo)) {
            $listaSeleccionada = Lista::where('id', $tipo)->where('user_id', $user->id)->first();
        }

        $query = $user->videojuegos()->withPivot('estado', 'puntuacion');
        $query = $this->aplicarFiltros($query, $tipo);
        if (is_numeric($tipo)) {
            $query = Videojuego::whereHas('listas', function ($q) use ($tipo, $user) {
                $q->where('listas.id', $tipo)
                ->where('listas.user_id', $user->id);
            });
        }

        $videojuegos = $query->paginate(27);

        return view('lista.videojuegos', compact('listas', 'videojuegos', 'tipo', 'listaSeleccionada'));
    }

    /**
     *Función para crear una lista, con el atributo tipo puedo reutiliarla para las 3 categorías sin hacer una diferente.
     */
    public function crearLista(Request $request)
    {
        //Validación
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo'   => 'required|in:pelicula,serie,videojuego'
        ]);
        //dd($request->all());
        $user = Auth::user();

        $user->listas()->create([
            'nombre' => $request->nombre,
            'tipo'   => $request->tipo
        ]);

        return back()->with('success', 'Lista creada correctamente');
    }

    /**
     * Función para eliminar películas de la lista, esta también la he hecho funcional para las tres categorías con tipo y detach es 
     * la única función de laravel que me ha servido.
     */
    public function editarLista(Request $request, $listaId, $tipo, $id)
    {
        $user = Auth::user();

        $lista = Lista::where('id', $listaId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($tipo === 'pelicula') {
            $lista->peliculas()->detach($id);
        }

        if ($tipo === 'serie') {
            $lista->series()->detach($id);
        }

        if ($tipo === 'videojuego') {
            $lista->videojuegos()->detach($id);
        }

        return back()->with('success', 'Elemento eliminado de la lista');
    }

    /**
     * Función de elomina la lista reutilizable, con redirección a la lista de pendientes que no podría se borrada, si el usuario
     * decide borrar una lista personalizada
     */
    public function eliminarLista($id, Request $request)
    {
        $user = Auth::user();

        $lista = Lista::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $lista->delete();

        $modulo = $request->input('modulo', 'peliculas');

        if ($modulo === 'series') {
            return redirect()
                ->route('lista_series_index', ['tipo' => 'pendientes'])
                ->with('success', 'Lista eliminada correctamente');
        }

        if ($modulo === 'videojuegos') {
            return redirect()
                ->route('lista_videojuegos_index', ['tipo' => 'pendientes'])
                ->with('success', 'Lista eliminada correctamente');
        }

        return redirect()->route('lista_index', ['tipo' => 'pendientes'])->with('success', 'Lista eliminada correctamente');
    }
}