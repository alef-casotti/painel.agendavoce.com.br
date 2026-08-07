<?php

namespace App\Http\Controllers;

use App\Models\Conteudo;
use App\Services\CarrosselHtmlGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CriadorConteudoController extends Controller
{
    public function __construct(
        protected CarrosselHtmlGenerator $carrosselHtmlGenerator
    ) {
    }

    /**
     * Listar conteúdos e calendário (apenas admin).
     */
    public function index(): View
    {
        $conteudos = Conteudo::query()
            ->orderBy('data_publicacao')
            ->orderBy('horario')
            ->get();

        $eventosPorData = $conteudos
            ->groupBy(fn (Conteudo $conteudo) => $conteudo->data_publicacao->format('Y-m-d'))
            ->map(fn ($itens) => $itens->map(fn (Conteudo $conteudo) => [
                'id' => $conteudo->id,
                'titulo' => $conteudo->titulo,
                'descricao' => $conteudo->descricao,
                'criador' => $conteudo->criador,
                'plataforma' => $conteudo->plataforma_label,
                'tipo_conteudo' => $conteudo->tipo_conteudo_label,
                'modelo' => $conteudo->modelo_label,
                'horario' => $conteudo->horario ? substr((string) $conteudo->horario, 0, 5) : null,
                'status' => $conteudo->status,
                'status_label' => $conteudo->status_label,
                'link' => $conteudo->link,
                'slides_count' => is_array($conteudo->slides) ? count($conteudo->slides) : 0,
                'url' => route('admin.criadores.show', $conteudo),
            ])->values())
            ->toArray();

        return view('admin.criadores.index', [
            'eventosPorData' => $eventosPorData,
            'totalConteudos' => $conteudos->count(),
        ]);
    }

    /**
     * Formulário de novo conteúdo.
     */
    public function create(): View
    {
        return view('admin.criadores.create', $this->formData());
    }

    /**
     * Salvar novo conteúdo.
     */
    public function store(Request $request): RedirectResponse
    {
        $dados = $this->validatedConteudo($request);
        $dados['criador'] = $request->user()->name;
        $dados['user_id'] = $request->user()->id;

        $conteudo = Conteudo::create($dados);

        return redirect()
            ->route('admin.criadores.show', $conteudo)
            ->with('success', 'Conteúdo criado e agendado no calendário!');
    }

    /**
     * Visualizar conteúdo.
     */
    public function show(Conteudo $conteudo): View
    {
        return view('admin.criadores.show', [
            'conteudo' => $conteudo,
        ]);
    }

    /**
     * Formulário de edição.
     */
    public function edit(Conteudo $conteudo): View
    {
        return view('admin.criadores.create', array_merge($this->formData(), [
            'conteudo' => $conteudo,
        ]));
    }

    /**
     * Atualizar conteúdo.
     */
    public function update(Request $request, Conteudo $conteudo): RedirectResponse
    {
        $dados = $this->validatedConteudo($request);

        $conteudo->update($dados);

        return redirect()
            ->route('admin.criadores.show', $conteudo)
            ->with('success', 'Conteúdo atualizado com sucesso!');
    }

    /**
     * Remover conteúdo.
     */
    public function destroy(Conteudo $conteudo): RedirectResponse
    {
        $conteudo->delete();

        return redirect()
            ->route('admin.criadores.index')
            ->with('success', 'Conteúdo removido com sucesso!');
    }

    protected function formData(): array
    {
        $modelos = Conteudo::modelosCarrossel();
        $slidesPadrao = [];

        foreach (array_keys($modelos) as $modeloKey) {
            $slidesPadrao[$modeloKey] = Conteudo::slidesPadrao($modeloKey);
        }

        return [
            'plataformas' => Conteudo::plataformas(),
            'tipos' => Conteudo::tipos(),
            'modelos' => $modelos,
            'slidesPadrao' => $slidesPadrao,
            'statuses' => Conteudo::statuses(),
            'conteudo' => null,
        ];
    }

    protected function validatedConteudo(Request $request): array
    {
        $dados = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'plataforma' => ['required', 'in:'.implode(',', array_keys(Conteudo::plataformas()))],
            'tipo_conteudo' => ['required', 'in:'.implode(',', array_keys(Conteudo::tipos()))],
            'modelo' => ['required', 'in:'.implode(',', array_keys(Conteudo::modelosCarrossel()))],
            'slides' => ['required', 'array', 'min:1'],
            'slides.*.titulo' => ['required', 'string', 'max:255'],
            'slides.*.texto' => ['required', 'string'],
            'slides.*.destaque' => ['nullable', 'string', 'max:100'],
            'data_publicacao' => ['required', 'date'],
            'horario' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'in:'.implode(',', array_keys(Conteudo::statuses()))],
            'link' => ['nullable', 'url', 'max:500'],
        ]);

        $slides = array_values(array_map(function (array $slide) {
            return [
                'titulo' => $slide['titulo'],
                'texto' => $slide['texto'],
                'destaque' => $slide['destaque'] ?? '',
            ];
        }, $dados['slides']));

        $dados['slides'] = $slides;
        $dados['html_gerado'] = $this->carrosselHtmlGenerator->generate($dados['modelo'], $slides);

        return $dados;
    }
}
