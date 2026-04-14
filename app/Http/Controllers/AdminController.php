<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ResenaPelicula;
use App\Models\ResenaSerie;
use App\Models\ResenaVideojuego;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Mostrar en el index todos los datos como estadísticas de usuarios, reseña y contenido
     */
    public function index()
    {
        $usuariosActivos = User::where('ultimo_login', '>=', Carbon::now()->subDays(7))->count();
        $totalUsuarios = User::count();

        $totalResenasPendientes =
            ResenaPelicula::where('aprobada', false)->count() +
            ResenaSerie::where('aprobada', false)->count() +
            ResenaVideojuego::where('aprobada', false)->count();

        $bloqueados = User::where('bloqueado', true)->count();

        return view('admin.index', compact('usuariosActivos', 'totalUsuarios', 'totalResenasPendientes', 'bloqueados'));
    }

    /**
     * Función para listar los usuarios.
     */
    public function usuarios()
    {
        $usuarios = User::latest()->get();

        return view('admin.usuarios', compact('usuarios'));
    }

    /**
     * Función para buscar usuarios por el nombre de usuario.
     */
    public function buscarUsuarios(Request $request)
    {
        $usuario = User::query();

        if ($request->filled('buscar')) {
            $usuario->where('nombre_usuario', 'like', '%' . $request->buscar . '%');
        }
        $usuarios = $usuario->orderBy('id', 'desc')->get();

        return view('admin.usuarios', compact('usuarios'));
    }

    /**
     * Función para cambiar el rol a los usuarios si eres administrador y que no puedan cambiarte el rol.
     */
    public function cambiarRol(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin' && $request->role !== 'admin') {
            return back()->with('error', 'No puedes modificar otro admin');
        }

        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'Rol actualizado');
    }

    /**
     * Función para bloquear un usuario.
     */
    public function bloquear($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'No puedes bloquear un admin');
        }

        $user->bloqueado = true;
        $user->save();

        return back()->with('success', 'Usuario bloqueado');
    }

    /**
     * Función para desbloquear un usuario bloqueado tanto por el admin o el gestor
     */
    public function desbloquear($id)
    {
        $user = User::findOrFail($id);

        $user->bloqueado = false;
        $user->save();

        return back()->with('success', 'Usuario desbloqueado');
    }

    /**
     * Función para eliminar un usuario si eres administrador solamente.
     */
    public function eliminar($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'No puedes eliminar un admin');
        }

        $user->delete();

        return back()->with('success', 'Usuario eliminado');
    }

    /**
     * Función para exportar datos a la data-table para poder listar los usuarios y sus datos para que el administrador
     * pueda consultarlos.
     */
    public function indexDataTable()
    {
        $usuarios = User::latest()->get();
        return view('admin.data-table-usuarios', compact('usuarios'));
    }
}