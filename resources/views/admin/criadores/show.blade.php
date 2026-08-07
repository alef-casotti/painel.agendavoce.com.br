@extends('layouts.app')

@section('title', $conteudo->titulo.' - Agenda Você')

@section('styles')
<style>
@include('admin.criadores._carrossel_preview_styles')
</style>
@endsection

@section('content')
@php
    $slides = $conteudo->slides ?? [];
@endphp
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100"
     x-data="visualizarConteudo(@js([
         'modelo' => $conteudo->modelo,
         'slides' => $slides,
     ]))"
     x-init="init()">
    <x-sidebar />
    <x-header />

    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8 space-y-6">
            <div class="space-y-4">
                <nav class="flex flex-wrap items-center gap-1.5 text-sm text-gray-500" aria-label="Breadcrumb">
                    <a href="{{ route('admin.criadores.index') }}" class="hover:text-blue-700 transition-colors">Criadores</a>
                    <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-800 font-medium truncate max-w-xs sm:max-w-md">{{ $conteudo->titulo }}</span>
                </nav>

                <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
                    <div class="min-w-0">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $conteudo->titulo }}</h1>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $conteudo->status === 'rascunho' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $conteudo->status === 'agendado' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $conteudo->status === 'publicado' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ $conteudo->status_label }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-700">
                                {{ $conteudo->tipo_conteudo_label }}
                            </span>
                            @if($conteudo->modelo_label)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-700">
                                    {{ $conteudo->modelo_label }}
                                </span>
                            @endif
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-700">
                                {{ $conteudo->plataforma_label }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-700">
                                {{ $conteudo->data_publicacao->format('d/m/Y') }}
                                @if($conteudo->horario)
                                    · {{ substr((string) $conteudo->horario, 0, 5) }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 w-full xl:w-auto shrink-0">
                        <a href="{{ route('admin.criadores.index') }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm font-medium">
                            Calendário
                        </a>
                        <button type="button" @click="baixarImagens()"
                                class="inline-flex items-center justify-center px-4 py-2.5 border border-blue-200 text-blue-700 rounded-lg hover:bg-blue-50 transition text-sm font-medium"
                                :disabled="baixando">
                            Baixar ZIP
                        </button>
                        <a href="{{ route('admin.criadores.edit', $conteudo) }}"
                           class="btn-primary inline-flex items-center justify-center text-sm">
                            Editar
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="space-y-6">
                <div class="card p-5 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Preview do carrossel</h2>
                            <p class="text-sm text-gray-500">
                                Página <span x-text="previewIndex + 1"></span> de <span x-text="slides.length"></span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="previewAnterior()"
                                    class="px-3 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 text-sm"
                                    :disabled="previewIndex === 0">Anterior</button>
                            <button type="button" @click="previewProximo()"
                                    class="px-3 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 text-sm"
                                    :disabled="previewIndex >= slides.length - 1">Próxima</button>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div class="card p-5 space-y-4">
                        <h2 class="text-base font-semibold text-gray-900">Detalhes</h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">Criador</dt>
                                <dd class="text-gray-900 font-medium text-right">{{ $conteudo->criador ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">Páginas</dt>
                                <dd class="text-gray-900 font-medium">{{ count($slides) }}</dd>
                            </div>
                            @if($conteudo->link)
                                <div>
                                    <dt class="text-gray-500 mb-1">Link</dt>
                                    <dd>
                                        <a href="{{ $conteudo->link }}" target="_blank" class="text-blue-600 hover:text-blue-700 break-all text-xs">
                                            {{ $conteudo->link }}
                                        </a>
                                    </dd>
                                </div>
                            @endif
                            @if($conteudo->descricao)
                                <div class="pt-2 border-t border-gray-100">
                                    <dt class="text-gray-500 mb-1">Legenda</dt>
                                    <dd class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $conteudo->descricao }}</dd>
                                </div>
                            @endif
                        </dl>

                        <form action="{{ route('admin.criadores.destroy', $conteudo) }}" method="POST"
                              onsubmit="return confirm('Remover este conteúdo?')"
                              class="pt-3 border-t border-gray-100">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">
                                Remover conteúdo
                            </button>
                        </form>
                    </div>

                    <div class="card p-4 space-y-2">
                        <h3 class="text-sm font-semibold text-gray-800 px-1">Páginas</h3>
                        <div class="space-y-2 max-h-[420px] overflow-y-auto">
                            <template x-for="(slide, index) in slides" :key="'list-' + index">
                                <button type="button"
                                        @click="previewIndex = index"
                                        class="w-full text-left p-3 rounded-xl border transition-colors"
                                        :class="previewIndex === index ? 'border-blue-300 bg-blue-50' : 'border-gray-100 hover:border-blue-200 bg-white'">
                                    <p class="text-[11px] text-gray-500 mb-0.5" x-text="'Página ' + (index + 1)"></p>
                                    <p class="text-sm font-medium text-gray-900 leading-snug" x-text="slide.titulo"></p>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
function visualizarConteudo(config) {
    return {
        modelo: config.modelo || 'organizacao',
        slides: Array.isArray(config.slides) ? config.slides : [],
        previewIndex: 0,
        previewScale: 0.28,
        baixando: false,

        init() {
            this.$nextTick(() => this.ajustarEscala());
            window.addEventListener('resize', () => this.ajustarEscala());
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
            this.previewScale = Math.min(0.42, Math.max(0.22, available / 1080));
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
                    const base64 = canvas.toDataURL('image/png').split(',')[1];
                    pasta.file(`slide-${String(i).padStart(2, '0')}.png`, base64, { base64: true });
                }

                if (exportRoot) {
                    exportRoot.style.transform = '';
                    exportRoot.style.zIndex = '';
                }

                const blob = await zip.generateAsync({ type: 'blob' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `carrossel-${this.modelo}.zip`;
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
