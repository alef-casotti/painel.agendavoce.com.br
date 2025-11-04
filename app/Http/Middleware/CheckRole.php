<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar autenticado.');
        }

        $user = auth()->user();

        // Admin tem acesso a tudo
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Verifica se o usuário tem uma das roles permitidas
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Se não tiver permissão, redireciona para o dashboard com erro
        return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar esta área.');
    }
}
