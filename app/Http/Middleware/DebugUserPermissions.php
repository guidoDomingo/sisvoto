<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DebugUserPermissions
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $debugInfo = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'role_id' => $user->role_id,
                'role_name' => $user->role ? $user->role->nombre : 'Sin rol',
                'role_slug' => $user->role ? $user->role->slug : 'Sin rol',
                'es_admin' => $user->esAdmin(),
                'puede_ver_votantes' => $user->puedeVerVotantes(),
                'url' => $request->url(),
                'route_name' => $request->route() ? $request->route()->getName() : 'Sin ruta',
            ];

            // Log para revisar en storage/logs/laravel.log
            Log::info('DEBUG USER PERMISSIONS', $debugInfo);

            // También mostrar en la respuesta si es desarrollo
            if (config('app.debug')) {
                session(['debug_user_info' => $debugInfo]);
            }
        }

        return $next($request);
    }
}