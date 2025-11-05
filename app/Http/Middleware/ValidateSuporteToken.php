<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSuporteToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = config('services.suporte.api_token');
        
        // Se o token não estiver configurado, bloqueia por segurança
        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token de API não configurado. Contate o administrador.'
            ], 500);
        }

        // Verifica o token no header Authorization: Bearer {token}
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader) {
            return response()->json([
                'success' => false,
                'message' => 'Token de autorização não fornecido.'
            ], 401);
        }

        // Remove o prefixo "Bearer " se existir
        $providedToken = str_replace('Bearer ', '', $authHeader);
        
        // Verifica se o token corresponde
        if ($providedToken !== $token) {
            return response()->json([
                'success' => false,
                'message' => 'Token de autorização inválido.'
            ], 401);
        }

        return $next($request);
    }
}

