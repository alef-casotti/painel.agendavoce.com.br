@extends('layouts.app')

@section('title', isset($conteudo) && $conteudo ? 'Editar Conteúdo - Agenda Você' : 'Novo Conteúdo - Agenda Você')

@section('styles')
<style>
@include('admin.criadores._carrossel_preview_styles')
</style>
@endsection

@section('content')
@php
    $isEdit = isset($conteudo) && $conteudo;
    $horarioAtual = $isEdit && $conteudo->horario ? substr((string) $conteudo->horario, 0, 5) : '';
@endphp
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100"
     x-data="criadorConteudo(@js([
         'plataformas' => $plataformas,
         'tipos' => $tipos,
         'modelos' => $modelos,
         'slidesPadrao' => $slidesPadrao,
         'statuses' => $statuses,
         'old' => [
             'titulo' => old('titulo', $isEdit ? $conteudo->titulo : ''),
             'descricao' => old('descricao', $isEdit ? $conteudo->descricao : ''),
             'plataforma' => old('plataforma', $isEdit ? $conteudo->plataforma : 'instagram'),
             'tipo_conteudo' => old('tipo_conteudo', $isEdit ? $conteudo->tipo_conteudo : 'carrossel'),
             'modelo' => old('modelo', $isEdit ? $conteudo->modelo : 'organizacao'),
             'slides' => old('slides', $isEdit ? ($conteudo->slides ?? []) : null),
             'data_publicacao' => old('data_publicacao', $isEdit ? $conteudo->data_publicacao->format('Y-m-d') : now()->format('Y-m-d')),
             'horario' => old('horario', $horarioAtual),
             'status' => old('status', $isEdit ? $conteudo->status : 'agendado'),
             'link' => old('link', $isEdit ? $conteudo->link : ''),
         ],
     ]))"
     x-init="init()">
    <x-sidebar />
    <x-header />

    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8 space-y-6 pb-28">
            <div class="space-y-4">
                <nav class="flex flex-wrap items-center gap-1.5 text-sm text-gray-500" aria-label="Breadcrumb">
                    <a href="{{ route('admin.criadores.index') }}" class="hover:text-blue-700 transition-colors">Criadores</a>
                    <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    @if($isEdit)
                        <a href="{{ route('admin.criadores.show', $conteudo) }}" class="hover:text-blue-700 transition-colors truncate max-w-[12rem]">{{ $conteudo->titulo }}</a>
                        <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="text-gray-800 font-medium">Editar</span>
                    @else
                        <span class="text-gray-800 font-medium">Novo conteúdo</span>
                    @endif
                </nav>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $isEdit ? 'Editar conteúdo' : 'Novo conteúdo' }}</h1>
                        <p class="mt-1.5 text-gray-600">
                            {{ $isEdit ? 'Atualize modelo, páginas e agendamento.' : 'Escolha o modelo, escreva as páginas e gere o carrossel.' }}
                        </p>
                    </div>
                    <a href="{{ $isEdit ? route('admin.criadores.show', $conteudo) : route('admin.criadores.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition w-full sm:w-auto">
                        {{ $isEdit ? 'Voltar à visualização' : 'Voltar ao calendário' }}
                    </a>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-100 text-xs font-medium text-gray-600">
                        <span class="w-5 h-5 rounded-full bg-blue-600 text-white inline-flex items-center justify-center text-[10px]">1</span>
                        Modelo
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-100 text-xs font-medium text-gray-600">
                        <span class="w-5 h-5 rounded-full bg-blue-600 text-white inline-flex items-center justify-center text-[10px]">2</span>
                        Agendamento
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-100 text-xs font-medium text-gray-600">
                        <span class="w-5 h-5 rounded-full bg-blue-600 text-white inline-flex items-center justify-center text-[10px]">3</span>
                        Páginas + preview
                    </span>
                </div>
            </div>

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <p class="font-semibold mb-2">Ops! Encontramos alguns problemas:</p>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ $isEdit ? route('admin.criadores.update', $conteudo) : route('admin.criadores.store') }}"
                  method="POST" class="space-y-6" @submit="baixando = false">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <!-- Seletores -->
                <div class="card p-5 sm:p-6 space-y-5 border-l-4 border-l-blue-500">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">1. Modelo</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Plataforma, tipo e roteiro do carrossel.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Plataforma *</label>
                            <select name="plataforma" x-model="plataforma" class="input-field" required>
                                <template x-for="(label, key) in plataformas" :key="key">
                                    <option :value="key" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de conteúdo *</label>
                            <select name="tipo_conteudo" x-model="tipoConteudo" class="input-field" required>
                                <template x-for="(label, key) in tipos" :key="key">
                                    <option :value="key" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Tipo de modelo *</label>
                        <input type="hidden" name="modelo" :value="modelo">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <template x-for="(info, key) in modelos" :key="key">
                                <button type="button"
                                        @click="if (modelo !== key) { modelo = key; aplicarModelo(); }"
                                        class="text-left p-4 rounded-xl border transition-all"
                                        :class="modelo === key
                                            ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-200 shadow-sm'
                                            : 'border-gray-200 bg-white hover:border-blue-200 hover:bg-blue-50/40'">
                                    <p class="font-semibold text-gray-900 text-sm" x-text="info.label"></p>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed" x-text="info.descricao"></p>
                                    <p class="text-[11px] text-blue-700/80 mt-2 font-medium" x-text="info.estrutura"></p>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Metadados do calendário -->
                <div class="card p-5 sm:p-6 space-y-5 border-l-4 border-l-indigo-400">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">2. Agendamento</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Quando e como esse conteúdo aparece no calendário.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Título *</label>
                            <input type="text" name="titulo" x-model="titulo" class="input-field" required
                                   placeholder="Ex: Carrossel dicas de agenda">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Criador</label>
                            <div class="input-field bg-gray-50 text-gray-700 cursor-default">
                                {{ $isEdit ? ($conteudo->criador ?: auth()->user()->name) : auth()->user()->name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status" x-model="status" class="input-field" required>
                                <template x-for="(label, key) in statuses" :key="key">
                                    <option :value="key" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data *</label>
                            <input type="date" name="data_publicacao" x-model="dataPublicacao" class="input-field" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Horário</label>
                            <input type="time" name="horario" x-model="horario" class="input-field">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Link (opcional)</label>
                            <input type="url" name="link" x-model="link" class="input-field" placeholder="https://...">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descrição / legenda</label>
                            <textarea name="descricao" x-model="descricao" rows="3" class="input-field"
                                      placeholder="Legenda para a publicação..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <!-- Editor de slides -->
                    <div class="card p-5 sm:p-6 space-y-4 border-l-4 border-l-emerald-400">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">3. Páginas do carrossel</h2>
                                <p class="text-sm text-gray-500"><span x-text="slides.length"></span> páginas · edição livre</p>
                            </div>
                            <button type="button" @click="adicionarSlide()"
                                    class="btn-primary inline-flex items-center text-sm px-3 py-2">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Página
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[720px] overflow-y-auto pr-1">
                            <template x-for="(slide, index) in slides" :key="index">
                                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-gray-700" x-text="'Página ' + (index + 1)"></span>
                                        <button type="button"
                                                @click="removerSlide(index)"
                                                x-show="slides.length > 1"
                                                class="text-xs font-medium text-red-600 hover:text-red-700">
                                            Remover
                                        </button>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Destaque / badge</label>
                                        <input type="text" :name="'slides[' + index + '][destaque]'" x-model="slide.destaque"
                                               class="input-field" placeholder="Ex: Dica, CTA, 1">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Título *</label>
                                        <input type="text" :name="'slides[' + index + '][titulo]'" x-model="slide.titulo"
                                               class="input-field" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Texto *</label>
                                        <textarea :name="'slides[' + index + '][texto]'" x-model="slide.texto" rows="3"
                                                  class="input-field" required></textarea>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="card p-5 sm:p-6 space-y-4 xl:sticky xl:top-20 h-fit">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Preview</h2>
                                <p class="text-sm text-gray-500">
                                    Página <span x-text="previewIndex + 1"></span> de <span x-text="slides.length"></span>
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="previewAnterior()"
                                        class="px-3 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50"
                                        :disabled="previewIndex === 0">
                                    Anterior
                                </button>
                                <button type="button" @click="previewProximo()"
                                        class="px-3 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50"
                                        :disabled="previewIndex >= slides.length - 1">
                                    Próxima
                                </button>
                            </div>
                        </div>

                        <div class="preview-scale-wrap" x-ref="previewWrap">
                            <div class="preview-viewport"
                                 :style="'width:' + (1080 * previewScale) + 'px; height:' + (1350 * previewScale) + 'px;'">
                                <div class="preview-stage" :style="'transform: scale(' + previewScale + ')'">
                                    <template x-for="(slide, index) in slides" :key="'prev-' + index">
                                        <div class="carrossel-slide-live"
                                             :class="'modelo-' + modelo"
                                             x-show="index === previewIndex">
                                            <div class="slide-glow slide-glow-a"></div>
                                            <div class="slide-glow slide-glow-b"></div>
                                            <div class="slide-ring"></div>
                                            <div class="slide-top">
                                                <span class="badge" x-show="slide.destaque"><span class="badge-label" x-text="slide.destaque"></span></span>
                                                <div class="accent-bar"></div>
                                            </div>
                                            <div class="slide-body">
                                                <h2 class="titulo" x-text="slide.titulo || 'Título'"></h2>
                                                <p class="texto" x-text="slide.texto || 'Texto da página'"></p>
                                            </div>
                                            <div class="footer">
                                                <div class="brand-wrap">
                                                    <span class="brand-dot"></span>
                                                    <span class="brand"><span class="brand-label">Agenda Você</span></span>
                                                </div>
                                                <span class="page">
                                                    <span x-text="index + 1"></span>
                                                    <span class="page-sep">/</span>
                                                    <span x-text="slides.length"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Container de export: precisa estar no viewport (não em -10000px) para fontes renderizarem certo -->
                        <div id="slides-export-root"
                             class="slides-export-root"
                             aria-hidden="true">
                            <template x-for="(slide, index) in slides" :key="'export-' + index">
                                <div class="carrossel-slide-live slide-preview"
                                     :class="'modelo-' + modelo"
                                     :data-slide-index="index">
                                    <div class="slide-glow slide-glow-a"></div>
                                    <div class="slide-glow slide-glow-b"></div>
                                    <div class="slide-ring"></div>
                                    <div class="slide-top">
                                        <span class="badge" x-show="slide.destaque"><span class="badge-label" x-text="slide.destaque"></span></span>
                                        <div class="accent-bar"></div>
                                    </div>
                                    <div class="slide-body">
                                        <h2 class="titulo" x-text="slide.titulo || 'Título'"></h2>
                                        <p class="texto" x-text="slide.texto || 'Texto da página'"></p>
                                    </div>
                                    <div class="footer">
                                        <div class="brand-wrap">
                                            <span class="brand-dot"></span>
                                            <span class="brand"><span class="brand-label">Agenda Você</span></span>
                                        </div>
                                        <span class="page">
                                            <span x-text="index + 1"></span>
                                            <span class="page-sep">/</span>
                                            <span x-text="slides.length"></span>
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <p class="text-xs text-gray-500" x-show="baixando">Gerando ZIP das imagens… aguarde.</p>
                    </div>
                </div>

                <div class="fixed bottom-0 inset-x-0 z-20 border-t border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
                    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-3 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:gap-3 lg:ml-64">
                        <button type="button" @click="baixarImagens()"
                                class="px-4 py-2.5 border border-blue-200 text-blue-700 rounded-lg hover:bg-blue-50 transition inline-flex items-center justify-center text-sm font-medium"
                                :disabled="baixando">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Baixar ZIP
                        </button>
                        <a href="{{ $isEdit ? route('admin.criadores.show', $conteudo) : route('admin.criadores.index') }}"
                           class="px-4 py-2.5 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition text-center text-sm font-medium">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-primary inline-flex items-center justify-center text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $isEdit ? 'Salvar alterações' : 'Salvar no calendário' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
function criadorConteudo(config) {
    return {
        plataformas: config.plataformas,
        tipos: config.tipos,
        modelos: config.modelos,
        slidesPadrao: config.slidesPadrao,
        statuses: config.statuses,

        titulo: config.old.titulo,
        descricao: config.old.descricao,
        plataforma: config.old.plataforma,
        tipoConteudo: config.old.tipo_conteudo,
        modelo: config.old.modelo,
        slides: [],
        dataPublicacao: config.old.data_publicacao,
        horario: config.old.horario,
        status: config.old.status,
        link: config.old.link,

        previewIndex: 0,
        previewScale: 0.28,
        baixando: false,

        init() {
            if (Array.isArray(config.old.slides) && config.old.slides.length) {
                this.slides = config.old.slides.map((s) => ({
                    titulo: s.titulo || '',
                    texto: s.texto || '',
                    destaque: s.destaque || '',
                }));
            } else {
                this.aplicarModelo(false);
            }

            this.$nextTick(() => this.ajustarEscala());
            window.addEventListener('resize', () => this.ajustarEscala());
        },

        aplicarModelo(resetTitulo = true) {
            const padrao = this.slidesPadrao[this.modelo] || [{ titulo: '', texto: '', destaque: '' }];
            this.slides = padrao.map((s) => ({ ...s }));
            this.previewIndex = 0;

            if (!this.titulo) {
                const label = this.modelos[this.modelo]?.label || 'Carrossel';
                this.titulo = `Carrossel ${label}`;
            }
        },

        adicionarSlide() {
            this.slides.push({ titulo: '', texto: '', destaque: String(this.slides.length) });
            this.previewIndex = this.slides.length - 1;
        },

        removerSlide(index) {
            if (this.slides.length <= 1) return;
            this.slides.splice(index, 1);
            if (this.previewIndex >= this.slides.length) {
                this.previewIndex = this.slides.length - 1;
            }
        },

        previewAnterior() {
            if (this.previewIndex > 0) this.previewIndex -= 1;
        },

        previewProximo() {
            if (this.previewIndex < this.slides.length - 1) this.previewIndex += 1;
        },

        ajustarEscala() {
            const wrap = this.$refs.previewWrap;
            if (!wrap) return;
            const available = wrap.clientWidth - 32;
            // Preview mais compacto na tela (export continua 1080x1350)
            this.previewScale = Math.min(0.30, Math.max(0.18, available / 1080));
        },

        async baixarImagens() {
            if (typeof html2canvas !== 'function') {
                alert('Não foi possível carregar o gerador de imagens. Tente novamente.');
                return;
            }
            if (typeof JSZip === 'undefined') {
                alert('Não foi possível carregar o gerador de ZIP. Tente novamente.');
                return;
            }

            this.baixando = true;
            await this.$nextTick();

            try {
                if (document.fonts && document.fonts.ready) {
                    await document.fonts.ready;
                }
                try {
                    await Promise.all([
                        document.fonts.load('700 24px "Source Sans Pro"'),
                        document.fonts.load('700 30px "Source Sans Pro"'),
                        document.fonts.load('700 72px "Playfair Display"'),
                        document.fonts.load('500 36px "Newsreader"'),
                    ]);
                } catch (e) {}

                const zip = new JSZip();
                const pasta = zip.folder('carrossel') || zip;
                const exportRoot = document.getElementById('slides-export-root');
                const nodes = [...document.querySelectorAll('#slides-export-root .slide-preview')];

                if (!nodes.length) {
                    throw new Error('Nenhuma página encontrada para exportar.');
                }

                // Traz para o viewport só durante a captura (melhor render de fonte)
                if (exportRoot) {
                    exportRoot.style.transform = 'none';
                    exportRoot.style.zIndex = '-1';
                }

                let i = 0;
                for (const node of nodes) {
                    i += 1;
                    const canvas = await html2canvas(node, {
                        scale: 1,
                        width: 1080,
                        height: 1350,
                        backgroundColor: null,
                        useCORS: true,
                        logging: false,
                        onclone(doc) {
                            // html2canvas desce o baseline de web fonts; sobe só o texto interno
                            // box-shadow vira mancha/quadrado escuro no PNG — remove só no clone
                            doc.querySelectorAll('.badge').forEach((el) => {
                                el.style.boxShadow = 'none';
                                el.style.filter = 'none';
                            });
                            doc.querySelectorAll('.badge-label').forEach((el) => {
                                el.style.position = 'relative';
                                el.style.top = '-10px';
                                el.style.display = 'inline-block';
                                el.style.lineHeight = '56px';
                                el.style.background = 'transparent';
                                el.style.boxShadow = 'none';
                                el.style.textShadow = 'none';
                            });
                            doc.querySelectorAll('.brand').forEach((el) => {
                                el.style.overflow = 'visible';
                                el.style.lineHeight = '30px';
                            });
                            doc.querySelectorAll('.brand-label').forEach((el) => {
                                el.style.position = 'relative';
                                el.style.top = '-16px';
                                el.style.display = 'inline-block';
                                el.style.lineHeight = '30px';
                                el.style.background = 'transparent';
                                el.style.boxShadow = 'none';
                                el.style.textShadow = 'none';
                            });
                        },
                    });
                    const dataUrl = canvas.toDataURL('image/png');
                    const base64 = dataUrl.split(',')[1];
                    pasta.file(`slide-${String(i).padStart(2, '0')}.png`, base64, { base64: true });
                }

                if (exportRoot) {
                    exportRoot.style.transform = '';
                    exportRoot.style.zIndex = '';
                }

                const blob = await zip.generateAsync({ type: 'blob' });
                const modeloSlug = this.modelo || 'carrossel';
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `carrossel-${modeloSlug}.zip`;
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(link.href);
            } catch (e) {
                console.error(e);
                const exportRoot = document.getElementById('slides-export-root');
                if (exportRoot) {
                    exportRoot.style.transform = '';
                    exportRoot.style.zIndex = '';
                }
                alert('Erro ao gerar o ZIP das imagens. Tente novamente.');
            } finally {
                this.baixando = false;
            }
        },
    };
}
</script>
@endsection
