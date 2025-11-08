<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagamento extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'titulo',
        'descricao',
        'categoria_id',
        'centro_custo_id',
        'fornecedor',
        'documento_referencia',
        'valor_previsto',
        'valor_pago',
        'data_competencia',
        'data_vencimento',
        'data_pagamento',
        'status',
        'metodo_pagamento',
        'recorrente',
        'parcela_atual',
        'parcelas_total',
        'metadados',
        'observacoes',
    ];

    protected $casts = [
        'valor_previsto' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'data_competencia' => 'date',
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
        'recorrente' => 'boolean',
        'metadados' => 'array',
        'deleted_at' => 'datetime',
    ];

    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_PAGO = 'pago';
    public const STATUS_ATRASADO = 'atrasado';
    public const STATUS_CANCELADO = 'cancelado';

    public static function metodosPagamento(): array
    {
        return [
            'boleto' => 'Boleto bancário',
            'cartao_credito' => 'Cartão de crédito',
            'cartao_debito' => 'Cartão de débito',
            'pix' => 'Pix',
            'transferencia' => 'Transferência bancária',
            'ted' => 'TED/DOC',
            'dinheiro' => 'Dinheiro',
            'outro' => 'Outro',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_PAGO => 'Pago',
            self::STATUS_ATRASADO => 'Atrasado',
            self::STATUS_CANCELADO => 'Cancelado',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(PagamentoCategoria::class, 'categoria_id');
    }

    public function centroCusto(): BelongsTo
    {
        return $this->belongsTo(CentroCusto::class, 'centro_custo_id');
    }

    public function getSaldoAttribute(): float
    {
        $valorPago = $this->valor_pago ?? 0;

        return (float) $valorPago - (float) $this->valor_previsto;
    }

    public function getMetodoPagamentoLabelAttribute(): ?string
    {
        if (! $this->metodo_pagamento) {
            return null;
        }

        return self::metodosPagamento()[$this->metodo_pagamento] ?? $this->metodo_pagamento;
    }
}

