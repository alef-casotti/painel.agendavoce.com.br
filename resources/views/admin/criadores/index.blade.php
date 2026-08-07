@extends('layouts.app')

@section('title', 'Criadores de Conteúdo - Agenda Você')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8 space-y-6"
             x-data="calendarioConteudos(@js($eventosPorData))"
             x-init="init()">

            <div class="space-y-4">
                <nav class="flex flex-wrap items-center gap-1.5 text-sm text-gray-500" aria-label="Breadcrumb">
                    <span class="text-gray-800 font-medium">Criadores</span>
                </nav>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                    <div class="min-w-0">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Criadores de Conteúdo</h1>
                        <p class="mt-1.5 text-gray-600 max-w-2xl">Planeje, visualize e acompanhe os carrosséis no calendário.</p>
                    </div>
                    <a href="{{ route('admin.criadores.create') }}" class="btn-primary inline-flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Criar conteúdo
                    </a>
                </div>
                <div class="flex flex-wrap gap-3">
                    <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-100 shadow-sm">
                        <span class="text-xs text-gray-500">Total</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $totalConteudos }}</span>
                    </div>
                    <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-100 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span class="text-xs text-gray-500">No dia selecionado</span>
                        <span class="text-sm font-semibold text-gray-900" x-text="eventosDoDia.length"></span>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
                <div class="xl:col-span-8 card overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-white/80 flex items-center justify-between gap-3">
                        <button type="button" @click="mesAnterior()"
                                class="p-2 rounded-lg text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                                aria-label="Mês anterior">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 capitalize" x-text="tituloMes"></h2>
                        <button type="button" @click="proximoMes()"
                                class="p-2 rounded-lg text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                                aria-label="Próximo mês">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="p-3 sm:p-5">
                        <div class="grid grid-cols-7 gap-1 mb-2">
                            <template x-for="dia in diasSemana" :key="dia">
                                <div class="text-center text-[11px] font-semibold text-gray-400 uppercase tracking-wide py-2" x-text="dia"></div>
                            </template>
                        </div>

                        <div class="grid grid-cols-7 gap-1.5">
                            <template x-for="(dia, index) in diasDoMes" :key="index">
                                <button type="button"
                                        @click="dia.date && selecionarDia(dia.date)"
                                        :disabled="!dia.date"
                                        class="min-h-[70px] sm:min-h-[92px] p-1.5 rounded-xl border text-left transition-all"
                                        :class="{
                                            'border-transparent bg-transparent': !dia.date,
                                            'border-gray-100 bg-white hover:border-blue-200 hover:bg-blue-50/40': dia.date && !dia.isSelected && !dia.isToday,
                                            'border-blue-600 bg-blue-50 ring-1 ring-blue-200 shadow-sm': dia.isSelected,
                                            'border-blue-200 bg-blue-50/30': dia.isToday && !dia.isSelected,
                                            'cursor-default': !dia.date,
                                            'cursor-pointer': dia.date
                                        }">
                                    <template x-if="dia.date">
                                        <div class="h-full flex flex-col">
                                            <span class="text-sm font-semibold leading-none mb-1"
                                                  :class="dia.isToday || dia.isSelected ? 'text-blue-700' : 'text-gray-700'"
                                                  x-text="dia.day"></span>
                                            <div class="mt-auto space-y-0.5">
                                                <template x-for="evento in dia.eventos.slice(0, 2)" :key="evento.id">
                                                    <div class="truncate text-[10px] sm:text-[11px] px-1.5 py-0.5 rounded-md font-medium"
                                                         :class="{
                                                             'bg-yellow-100 text-yellow-800': evento.status === 'rascunho',
                                                             'bg-blue-100 text-blue-800': evento.status === 'agendado',
                                                             'bg-green-100 text-green-800': evento.status === 'publicado'
                                                         }"
                                                         x-text="evento.titulo"></div>
                                                </template>
                                                <div x-show="dia.eventos.length > 2"
                                                     class="text-[10px] text-gray-500 px-1"
                                                     x-text="'+' + (dia.eventos.length - 2) + ' mais'"></div>
                                            </div>
                                        </div>
                                    </template>
                                </button>
                            </template>
                        </div>

                        <div class="mt-5 pt-4 border-t border-gray-100 flex flex-wrap gap-4 text-xs text-gray-600">
                            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span> Rascunho</span>
                            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Agendado</span>
                            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Publicado</span>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-4 card overflow-hidden xl:sticky xl:top-20">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
                        <h3 class="text-base font-semibold text-gray-900">Conteúdos do dia</h3>
                        <p class="text-sm text-gray-500 capitalize mt-0.5" x-text="dataSelecionadaLabel"></p>
                    </div>

                    <div class="p-4 space-y-3 max-h-[640px] overflow-y-auto">
                        <template x-if="eventosDoDia.length === 0">
                            <div class="text-center py-12 px-4">
                                <div class="inline-flex items-center justify-center w-14 h-14 mb-3 bg-blue-50 rounded-2xl">
                                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-800 mb-1">Nada agendado</p>
                                <p class="text-sm text-gray-500 mb-4">Crie um carrossel para este dia.</p>
                                <a href="{{ route('admin.criadores.create') }}" class="btn-primary inline-flex text-sm px-4 py-2">
                                    Criar conteúdo
                                </a>
                            </div>
                        </template>

                        <template x-for="evento in eventosDoDia" :key="evento.id">
                            <article class="group rounded-xl border border-gray-100 bg-white p-4 hover:border-blue-200 hover:shadow-sm transition-all">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <a :href="evento.url" class="font-semibold text-gray-900 text-sm leading-snug group-hover:text-blue-700" x-text="evento.titulo"></a>
                                    <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                          :class="{
                                              'bg-yellow-100 text-yellow-800': evento.status === 'rascunho',
                                              'bg-blue-100 text-blue-800': evento.status === 'agendado',
                                              'bg-green-100 text-green-800': evento.status === 'publicado'
                                          }"
                                          x-text="evento.status_label"></span>
                                </div>
                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-500 mb-3">
                                    <span x-show="evento.horario" x-text="evento.horario"></span>
                                    <span x-show="evento.modelo" x-text="evento.modelo"></span>
                                    <span x-show="evento.slides_count" x-text="evento.slides_count + ' págs'"></span>
                                    <span x-text="evento.plataforma"></span>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-gray-50">
                                    <a :href="evento.url" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Abrir</a>
                                    <form :action="`{{ url('admin/criadores') }}/${evento.id}`" method="POST"
                                          onsubmit="return confirm('Remover este conteúdo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700">Remover</button>
                                    </form>
                                </div>
                            </article>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
function calendarioConteudos(eventosPorData) {
    return {
        eventosPorData: eventosPorData || {},
        ano: new Date().getFullYear(),
        mes: new Date().getMonth(),
        dataSelecionada: null,
        diasSemana: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],

        init() {
            this.dataSelecionada = this.formatDate(new Date());
        },

        get tituloMes() {
            return new Date(this.ano, this.mes, 1)
                .toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });
        },

        get dataSelecionadaLabel() {
            if (!this.dataSelecionada) return '';
            const [y, m, d] = this.dataSelecionada.split('-').map(Number);
            return new Date(y, m - 1, d).toLocaleDateString('pt-BR', {
                weekday: 'long', day: '2-digit', month: 'long', year: 'numeric'
            });
        },

        get eventosDoDia() {
            if (!this.dataSelecionada) return [];
            return this.eventosPorData[this.dataSelecionada] || [];
        },

        get diasDoMes() {
            const primeiroDia = new Date(this.ano, this.mes, 1);
            const ultimoDia = new Date(this.ano, this.mes + 1, 0);
            const inicioSemana = primeiroDia.getDay();
            const totalDias = ultimoDia.getDate();
            const hoje = this.formatDate(new Date());
            const dias = [];

            for (let i = 0; i < inicioSemana; i++) {
                dias.push({ date: null, day: '', eventos: [], isToday: false, isSelected: false });
            }

            for (let day = 1; day <= totalDias; day++) {
                const date = this.formatDate(new Date(this.ano, this.mes, day));
                dias.push({
                    date,
                    day,
                    eventos: this.eventosPorData[date] || [],
                    isToday: date === hoje,
                    isSelected: date === this.dataSelecionada,
                });
            }

            while (dias.length % 7 !== 0) {
                dias.push({ date: null, day: '', eventos: [], isToday: false, isSelected: false });
            }

            return dias;
        },

        formatDate(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        },

        selecionarDia(date) { this.dataSelecionada = date; },
        mesAnterior() {
            if (this.mes === 0) { this.mes = 11; this.ano -= 1; }
            else { this.mes -= 1; }
        },
        proximoMes() {
            if (this.mes === 11) { this.mes = 0; this.ano += 1; }
            else { this.mes += 1; }
        },
    };
}
</script>
@endsection
