<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PagamentoCategoria extends Model
{
    use HasFactory;

    protected $table = 'pagamento_categorias';

    protected $fillable = [
        'nome',
        'slug',
        'tipo',
        'descricao',
        'ativo',
        'padrao',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'padrao' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $categoria): void {
            if (empty($categoria->slug)) {
                $categoria->slug = Str::slug($categoria->nome);
            }
        });

        static::updating(function (self $categoria): void {
            if ($categoria->isDirty('nome')) {
                $categoria->slug = Str::slug($categoria->nome);
            }
        });
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'categoria_id');
    }
}

