<?php

namespace App\Services;

use App\Models\Conteudo;
use Illuminate\Support\Facades\View;

class CarrosselHtmlGenerator
{
    /**
     * Gera o HTML completo do carrossel a partir do modelo e slides.
     *
     * @param  array<int, array{titulo?: string, texto?: string, destaque?: string}>  $slides
     */
    public function generate(string $modelo, array $slides): string
    {
        $modelos = array_keys(Conteudo::modelosCarrossel());
        $viewModelo = in_array($modelo, $modelos, true) ? $modelo : Conteudo::MODELO_ORGANIZACAO;

        return View::make('admin.criadores.templates.carrossel.'.$viewModelo, [
            'slides' => $slides,
            'modelo' => $viewModelo,
        ])->render();
    }
}
