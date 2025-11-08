<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CentroCusto extends Model
{
    use HasFactory;

    protected $table = 'centros_custo';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $centro): void {
            if (empty($centro->slug)) {
                $centro->slug = Str::slug($centro->nome);
            }
        });

        static::updating(function (self $centro): void {
            if ($centro->isDirty('nome')) {
                $centro->slug = Str::slug($centro->nome);
            }
        });
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'centro_custo_id');
    }
}

