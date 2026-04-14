<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //Registro de usuarios
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nombre_usuario' => 'required|string|max:255|unique:users,nombre_usuario',
            'email' => 'required|email|unique:users,email',
            'fecha_nacimiento' => 'required|date|before:today',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->nombre_usuario = $validated['nombre_usuario'];
        $user->email = $validated['email'];
        $user->fecha_nacimiento = $validated['fecha_nacimiento'];
        $user->password = Hash::make($validated['password']);
        $user->role = 'usuario';

        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('inicio')
            ->with('mensaje', 'Registro completado con éxito');
    }

    //Login. Aquí lo he puesto para que si ha sido bloqueado no pueda entrar
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {

            $request->session()->regenerate();

            $user = Auth::user();

            //Controlar si esta bloqueado
            if ($user->bloqueado) {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Tu cuenta ha sido bloqueada por un administrador.'
                ]);
            }

            // Guardo el ultimo login para saber si ha tenido un ingreso a la plataforma en menos de 7 días 
            $user->update([
                'ultimo_login' => now()
            ]);

            //Según el rol reedirijo la ruta para el inicio.
            if ($user->role === 'admin') {
                return redirect()->route('inicio');
            }

            if ($user->role === 'gestor') {
                return redirect()->route('inicio');
            }

            return redirect()->route('inicio');
        }

        return back()->withErrors(['login' => 'Email o contraseña incorrectos.',])->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}