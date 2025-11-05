<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'assunto',
        'status',
        'prioridade',
        'user_id',
    ];

    /**
     * Relacionamento com mensagens
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Relacionamento com usuário que atendeu o ticket
     */
    public function atendente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Verifica se o ticket está aberto
     */
    public function estaAberto(): bool
    {
        return $this->status === 'aberto';
    }

    /**
     * Verifica se o ticket pode ser fechado
     */
    public function podeFechar(): bool
    {
        return in_array($this->status, ['aberto', 'em_andamento', 'aguardando_cliente']);
    }

    /**
     * Verifica se é alta prioridade
     */
    public function isAltaPrioridade(): bool
    {
        return $this->prioridade === 'alta';
    }
}
