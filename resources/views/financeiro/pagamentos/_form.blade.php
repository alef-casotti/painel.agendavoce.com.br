@php
    $pagamento = $pagamento ?? null;
    $metodosPagamento = $metodosPagamento ?? \App\Models\Pagamento::metodosPagamento();
@endphp

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
    <div class="xl:col-span-8 space-y-6">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações principais</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Título *</label>
                    <input type="text" name="titulo" value="{{ old('titulo', optional($pagamento)->titulo) }}" class="input-field" placeholder="Ex: Assinatura AWS Outubro">
                    @error('titulo')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fornecedor</label>
                    <input type="text" name="fornecedor" value="{{ old('fornecedor', optional($pagamento)->fornecedor) }}" class="input-field" placeholder="Ex: Amazon Web Services">
                    @error('fornecedor')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                    <select name="categoria_id" class="input-field">
                        <option value="">Selecione...</option>
                        @forelse($categorias as $categoria)
                            <option value="{{ $categoria->id }}" @selected(old('categoria_id', optional($pagamento)->categoria_id) == $categoria->id)>
                                {{ $categoria->nome }}
                            </option>
                        @empty
                            <option value="" disabled>Nenhuma categoria cadastrada</option>
                        @endforelse
                    </select>
                    @error('categoria_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Centro de custo</label>
                    <select name="centro_custo_id" class="input-field">
                        <option value="">Selecione...</option>
                        @forelse($centrosCusto as $centro)
                            <option value="{{ $centro->id }}" @selected(old('centro_custo_id', optional($pagamento)->centro_custo_id) == $centro->id)>
                                {{ $centro->nome }}
                            </option>
                        @empty
                            <option value="" disabled>Nenhum centro de custo cadastrado</option>
                        @endforelse
                    </select>
                    @error('centro_custo_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Documento/Referência</label>
                    <input type="text" name="documento_referencia" value="{{ old('documento_referencia', optional($pagamento)->documento_referencia) }}" class="input-field" placeholder="Ex: NF 12345">
                    @error('documento_referencia')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" class="input-field">
                        @foreach($statuses as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" @selected(old('status', optional($pagamento)->status ?? 'pendente') === $statusValue)>
                                {{ $statusLabel }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 mt-4">Descrição</label>
                <textarea name="descricao" rows="3" class="input-field" placeholder="Detalhes adicionais sobre o pagamento">{{ old('descricao', optional($pagamento)->descricao) }}</textarea>
                @error('descricao')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Datas e recorrência</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data competência</label>
                    <input type="date" name="data_competencia" value="{{ old('data_competencia', optional($pagamento)?->data_competencia?->format('Y-m-d')) }}" class="input-field">
                    @error('data_competencia')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data vencimento</label>
                    <input type="date" name="data_vencimento" value="{{ old('data_vencimento', optional($pagamento)?->data_vencimento?->format('Y-m-d')) }}" class="input-field">
                    @error('data_vencimento')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data pagamento</label>
                    <input type="date" name="data_pagamento" value="{{ old('data_pagamento', optional($pagamento)?->data_pagamento?->format('Y-m-d')) }}" class="input-field">
                    @error('data_pagamento')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Método de pagamento</label>
                    <select name="metodo_pagamento" class="input-field">
                        <option value="">Selecione...</option>
                        @foreach($metodosPagamento as $valor => $label)
                            <option value="{{ $valor }}" @selected(old('metodo_pagamento', optional($pagamento)->metodo_pagamento) === $valor)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('metodo_pagamento')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center space-x-3 mt-1">
                    <input type="hidden" name="recorrente" value="0">
                    <input type="checkbox" name="recorrente" value="1" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" @checked(old('recorrente', optional($pagamento)->recorrente))>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Despesa recorrente</p>
                        <p class="text-xs text-gray-500">Marque se este pagamento acontece com frequência (mensal, anual...)</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Parcela atual</label>
                    <input type="number" min="1" name="parcela_atual" value="{{ old('parcela_atual', optional($pagamento)->parcela_atual) }}" class="input-field" placeholder="Ex: 2">
                    @error('parcela_atual')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total de parcelas</label>
                    <input type="number" min="1" name="parcelas_total" value="{{ old('parcelas_total', optional($pagamento)->parcelas_total) }}" class="input-field" placeholder="Ex: 10">
                    @error('parcelas_total')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="xl:col-span-4 space-y-6">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Valores</h3>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valor previsto *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-500">R$</span>
                        <input type="text" name="valor_previsto" data-money-input value="{{ old('valor_previsto', optional($pagamento)->valor_previsto ? number_format(optional($pagamento)->valor_previsto, 2, '.', '') : '') }}" class="input-field pl-10" placeholder="0.00" inputmode="decimal" autocomplete="off">
                    </div>
                    @error('valor_previsto')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valor pago</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-500">R$</span>
                        <input type="text" name="valor_pago" data-money-input value="{{ old('valor_pago', optional($pagamento)->valor_pago ? number_format(optional($pagamento)->valor_pago, 2, '.', '') : '') }}" class="input-field pl-10" placeholder="0.00" inputmode="decimal" autocomplete="off">
                    </div>
                    @error('valor_pago')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Observações</h3>
            <textarea name="observacoes" rows="6" class="input-field" placeholder="Informações adicionais, links de comprovantes, observações do financeiro...">{{ old('observacoes', optional($pagamento)->observacoes) }}</textarea>
            @error('observacoes')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const moneyInputs = document.querySelectorAll('[data-money-input]');

                moneyInputs.forEach((input) => {
                    const formatValue = (rawValue) => {
                        const digits = rawValue.replace(/\D/g, '');

                        if (!digits) {
                            return '';
                        }

                        const number = parseInt(digits, 10) / 100;

                        return number.toFixed(2);
                    };

                    input.addEventListener('input', (event) => {
                        input.value = formatValue(event.target.value);

                        requestAnimationFrame(() => {
                            input.setSelectionRange(input.value.length, input.value.length);
                        });
                    });

                    input.addEventListener('blur', (event) => {
                        const value = event.target.value;

                        if (!value) {
                            return;
                        }

                        const digits = value.replace(/\D/g, '');

                        if (!digits) {
                            event.target.value = '';
                            return;
                        }

                        const number = parseInt(digits, 10) / 100;
                        event.target.value = number.toFixed(2);
                    });

                    input.addEventListener('focus', (event) => {
                        if (!event.target.value) {
                            return;
                        }

                        requestAnimationFrame(() => event.target.select());
                    });

                    if (input.value) {
                        input.value = formatValue(input.value);
                    }
                });
            });
        </script>
    @endpush
@endonce

