<?php

namespace App\Http\Controllers;

use App\Models\CentroCusto;
use App\Models\Pagamento;
use App\Models\PagamentoCategoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PagamentoController extends Controller
{
    protected array $categoriasPadrao = [
        ['nome' => 'Serviços em Nuvem', 'tipo' => 'fixa', 'descricao' => 'Gastos recorrentes com infraestrutura e servidores', 'padrao' => true],
        ['nome' => 'Marketing e Aquisição', 'tipo' => 'variavel', 'descricao' => 'Investimentos para aquisição de clientes'],
        ['nome' => 'Ferramentas e Softwares', 'tipo' => 'fixa', 'descricao' => 'Assinaturas e ferramentas de apoio'],
        ['nome' => 'Financeiro e Bancário', 'tipo' => 'variavel', 'descricao' => 'Tarifas bancárias e custos financeiros'],
    ];

    protected array $centrosPadrao = [
        ['nome' => 'Operações'],
        ['nome' => 'Tecnologia'],
        ['nome' => 'Marketing'],
        ['nome' => 'Administrativo'],
    ];

    /**
     * Display a listing of the payments.
     */
    public function index(Request $request): View
    {
        $this->garantirDadosPadrao();

        $query = Pagamento::query()
            ->with(['categoria', 'centroCusto'])
            ->orderByDesc('data_vencimento')
            ->orderByDesc('created_at');

        $statusFiltro = $request->input('status');

        if ($statusFiltro !== null && array_key_exists($statusFiltro, Pagamento::statuses())) {
            $query->where('status', $statusFiltro);
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->integer('categoria_id'));
        }

        if ($request->filled('centro_custo_id')) {
            $query->where('centro_custo_id', $request->integer('centro_custo_id'));
        }

        if ($request->filled('periodo_de')) {
            $query->whereDate('data_competencia', '>=', $request->date('periodo_de'));
        }

        if ($request->filled('periodo_ate')) {
            $query->whereDate('data_competencia', '<=', $request->date('periodo_ate'));
        }

        if ($request->filled('busca')) {
            $busca = $request->string('busca');
            $query->where(function ($subQuery) use ($busca) {
                $subQuery->where('titulo', 'like', "%{$busca}%")
                    ->orWhere('descricao', 'like', "%{$busca}%")
                    ->orWhere('fornecedor', 'like', "%{$busca}%")
                    ->orWhere('documento_referencia', 'like', "%{$busca}%");
            });
        }

        $pagamentos = $query->paginate(15)->withQueryString();

        $resumo = [
            'total_previsto' => Pagamento::sum('valor_previsto'),
            'total_pago' => Pagamento::where('status', Pagamento::STATUS_PAGO)->sum('valor_pago'),
            'pendentes' => Pagamento::where('status', Pagamento::STATUS_PENDENTE)->count(),
            'atrasados' => Pagamento::where('status', Pagamento::STATUS_ATRASADO)->count(),
        ];

        return view('financeiro.pagamentos.index', [
            'pagamentos' => $pagamentos,
            'categorias' => PagamentoCategoria::orderBy('nome')->get(),
            'centrosCusto' => CentroCusto::orderBy('nome')->get(),
            'statuses' => Pagamento::statuses(),
            'metodosPagamento' => Pagamento::metodosPagamento(),
            'filtros' => $request->only(['status', 'categoria_id', 'centro_custo_id', 'periodo_de', 'periodo_ate', 'busca']),
            'resumo' => $resumo,
        ]);
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(): View
    {
        $this->garantirDadosPadrao();

        return view('financeiro.pagamentos.create', [
            'categorias' => PagamentoCategoria::where('ativo', true)->orderBy('nome')->get(),
            'centrosCusto' => CentroCusto::where('ativo', true)->orderBy('nome')->get(),
            'statuses' => Pagamento::statuses(),
            'metodosPagamento' => Pagamento::metodosPagamento(),
        ]);
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePagamento($request);

        $validated['valor_previsto'] = $this->normalizeMoney($validated['valor_previsto']);

        if (! empty($validated['valor_pago'])) {
            $validated['valor_pago'] = $this->normalizeMoney($validated['valor_pago']);
        }

        Pagamento::create($validated);

        return redirect()
            ->route('financeiro.pagamentos.index')
            ->with('success', 'Pagamento cadastrado com sucesso.');
    }

    /**
     * Display the specified payment.
     */
    public function show(Pagamento $pagamento): View
    {
        $pagamento->load(['categoria', 'centroCusto']);

        return view('financeiro.pagamentos.show', [
            'pagamento' => $pagamento,
        ]);
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Pagamento $pagamento): View
    {
        $this->garantirDadosPadrao();

        return view('financeiro.pagamentos.edit', [
            'pagamento' => $pagamento->load(['categoria', 'centroCusto']),
            'categorias' => PagamentoCategoria::where('ativo', true)->orderBy('nome')->get(),
            'centrosCusto' => CentroCusto::where('ativo', true)->orderBy('nome')->get(),
            'statuses' => Pagamento::statuses(),
            'metodosPagamento' => Pagamento::metodosPagamento(),
        ]);
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, Pagamento $pagamento): RedirectResponse
    {
        $validated = $this->validatePagamento($request);

        $validated['valor_previsto'] = $this->normalizeMoney($validated['valor_previsto']);

        if (! empty($validated['valor_pago'])) {
            $validated['valor_pago'] = $this->normalizeMoney($validated['valor_pago']);
        } else {
            $validated['valor_pago'] = null;
        }

        $pagamento->update($validated);

        return redirect()
            ->route('financeiro.pagamentos.index')
            ->with('success', 'Pagamento atualizado com sucesso.');
    }

    /**
     * Remove the specified payment.
     */
    public function destroy(Pagamento $pagamento): RedirectResponse
    {
        $pagamento->delete();

        return redirect()
            ->route('financeiro.pagamentos.index')
            ->with('success', 'Pagamento removido com sucesso.');
    }

    /**
     * Validate payment request data.
     *
     * @return array<string, mixed>
     */
    protected function validatePagamento(Request $request): array
    {
        $statuses = implode(',', array_keys(Pagamento::statuses()));
        $metodosPagamento = implode(',', array_keys(Pagamento::metodosPagamento()));

        return $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'categoria_id' => ['nullable', 'exists:pagamento_categorias,id'],
            'centro_custo_id' => ['nullable', 'exists:centros_custo,id'],
            'fornecedor' => ['nullable', 'string', 'max:255'],
            'documento_referencia' => ['nullable', 'string', 'max:100'],
            'valor_previsto' => ['required'],
            'valor_pago' => ['nullable'],
            'data_competencia' => ['nullable', 'date'],
            'data_vencimento' => ['nullable', 'date'],
            'data_pagamento' => ['nullable', 'date'],
            'status' => ['required', 'in:' . $statuses],
            'metodo_pagamento' => ['nullable', 'in:' . $metodosPagamento],
            'recorrente' => ['boolean'],
            'parcela_atual' => ['nullable', 'integer', 'min:1'],
            'parcelas_total' => ['nullable', 'integer', 'min:1'],
            'observacoes' => ['nullable', 'string'],
        ]);
    }

    /**
     * Normalize money string input to database format.
     */
    protected function normalizeMoney(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $cleanValue = trim(str_replace(['R$', ' '], '', $value));
        $hasComma = str_contains($cleanValue, ',');
        $hasDot = str_contains($cleanValue, '.');

        if ($hasComma && $hasDot) {
            // Format 1.234,56 -> remove thousand separators and replace comma
            $cleanValue = str_replace('.', '', $cleanValue);
            $cleanValue = str_replace(',', '.', $cleanValue);
        } elseif ($hasComma) {
            // Format 1234,56
            $cleanValue = str_replace(',', '.', $cleanValue);
        } else {
            // Remove any thousands separators leftover
            $cleanValue = preg_replace('/[^\d.-]/', '', $cleanValue);
        }

        return number_format((float) $cleanValue, 2, '.', '');
    }

    protected function garantirDadosPadrao(): void
    {
        if (! PagamentoCategoria::exists()) {
            foreach ($this->categoriasPadrao as $categoria) {
                PagamentoCategoria::firstOrCreate(
                    ['slug' => Str::slug($categoria['nome'])],
                    $categoria
                );
            }
        }

        if (! CentroCusto::exists()) {
            foreach ($this->centrosPadrao as $centro) {
                CentroCusto::firstOrCreate(
                    ['slug' => Str::slug($centro['nome'])],
                    $centro
                );
            }
        }
    }
}

