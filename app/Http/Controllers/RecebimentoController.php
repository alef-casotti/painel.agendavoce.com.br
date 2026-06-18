<?php

namespace App\Http\Controllers;

use App\Services\AgendaVoceUsuarioService;
use Illuminate\View\View;

class RecebimentoController extends Controller
{
    public function __construct(
        private readonly AgendaVoceUsuarioService $usuarioService
    ) {
    }

    /**
     * Listar recebimentos (assinaturas ativas via API).
     */
    public function index(): View
    {
        $response = $this->usuarioService->fetchAssinaturasAtivas();
        $recebimentos = $response['items'];
        $error = $response['error'];

        $resumo = [
            'total_mrr' => $recebimentos->sum('valor'),
            'quantidade' => $recebimentos->count(),
        ];

        return view('financeiro.recebimentos.index', [
            'recebimentos' => $recebimentos,
            'resumo' => $resumo,
            'error' => $error,
        ]);
    }
}
