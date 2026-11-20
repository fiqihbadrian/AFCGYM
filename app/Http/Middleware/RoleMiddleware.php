<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Batasi akses route berdasarkan role user.
     * Contoh pemakaian: ->middleware('role:1') untuk admin, 'role:0' untuk member.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            return redirect()->route('backend.login');
        }

        if (Auth::user()->role != $role) {
            Auth::logout();

            return redirect()->route('backend.login')->with('error', 'Anda tidak memiliki hak akses');
        }

        return $next($request);
    }
}
