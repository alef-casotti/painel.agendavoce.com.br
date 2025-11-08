<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
    /**
     * Exibir área financeira
     */
    public function index()
    {
        $hoje = Carbon::today();
        $inicioMes = $hoje->copy()->startOfMonth();
        $fimMes = $hoje->copy()->endOfMonth();

        $resumo = [
            'total_previsto_mes' => Pagamento::whereBetween('data_competencia', [$inicioMes, $fimMes])->sum('valor_previsto'),
            'total_pago_mes' => Pagamento::whereBetween('data_pagamento', [$inicioMes, $fimMes])->sum('valor_pago'),
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

        return view('financeiro.index', [
            'resumo' => $resumo,
            'proximosPagamentos' => $proximosPagamentos,
            'ultimosPagamentos' => $ultimosPagamentos,
        ]);
    }
}
