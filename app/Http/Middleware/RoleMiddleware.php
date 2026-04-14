<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $roles)
    {
        if (!Auth::check()) {
            abort(401);
        }
        
        $rolesArray = preg_split('/[|,]/', $roles);

        $rolesArray = array_map('trim', $rolesArray);

        if (!in_array(Auth::user()->role, $rolesArray)) {
            abort(403);
        }

        return $next($request);
    }
}