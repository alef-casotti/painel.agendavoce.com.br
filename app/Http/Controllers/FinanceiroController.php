<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Services\AgendaVoceUsuarioService;
use Carbon\Carbon;
use Illuminate\View\View;

class FinanceiroController extends Controller
{
    public function __construct(
        private readonly AgendaVoceUsuarioService $usuarioService
    ) {
    }

    /**
     * Exibir área financeira
     */
    public function index(): View
    {
        $hoje = Carbon::today();
        $inicioMes = $hoje->copy()->startOfMonth();
        $fimMes = $hoje->copy()->endOfMonth();

        $statsResponse = $this->usuarioService->fetchStats();
        $stats = $statsResponse['data'];
        $receitaError = $statsResponse['error'];

        $mrr = $stats ? (float) data_get($stats, 'em_operacao.valor', 0) : null;
        $clientesPagantes = $stats ? (int) data_get($stats, 'em_operacao.quantidade', 0) : null;

        $despesasPagasMes = (float) Pagamento::whereBetween('data_pagamento', [$inicioMes, $fimMes])->sum('valor_pago');
        $despesasPrevistasMes = (float) Pagamento::whereBetween('data_competencia', [$inicioMes, $fimMes])->sum('valor_previsto');
        $resultado = $mrr !== null ? $mrr - $despesasPagasMes : null;

        $resumo = [
            'mrr' => $mrr,
            'clientes_pagantes' => $clientesPagantes,
            'despesas_pagas_mes' => $despesasPagasMes,
            'despesas_previstas_mes' => $despesasPrevistasMes,
            'resultado' => $resultado,
            'total_previsto_mes' => $despesasPrevistasMes,
            'total_pago_mes' => $despesasPagasMes,
            'pendentes' => Pagamento::whereIn('status', [Pagamento::STATUS_PENDENTE, Pagamento::STATUS_ATRASADO])->count(),
            'pagamentos_mes' => Pagamento::whereBetween('data_competencia', [$inicioMes, $fimMes])->count(),
        ];

        $proximosPagamentos = Pagamento::query()
            ->with('categoria')
            ->whereNotNull('data_vencimento')
            ->whereIn('status', [Pagamento::STATUS_PENDENTE, Pagamento::STATUS_ATRASADO])
            ->orderBy('data_vencimento')
            ->limit(5)
            ->get();

        $ultimosPagamentos = Pagamento::query()
            ->with(['categoria', 'centroCusto'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $assinaturasResponse = $this->usuarioService->fetchAssinaturasAtivas();
        $ultimosRecebimentos = $assinaturasResponse['items']->take(5);

        if ($receitaError === null && $assinaturasResponse['error'] !== null) {
            $receitaError = $assinaturasResponse['error'];
        }

        return view('financeiro.index', [
            'resumo' => $resumo,
            'receitaError' => $receitaError,
            'proximosPagamentos' => $proximosPagamentos,
            'ultimosPagamentos' => $ultimosPagamentos,
            'ultimosRecebimentos' => $ultimosRecebimentos,
        ]);
    }
}
