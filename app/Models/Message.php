<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'sender_email',
        'sender_type',
        'message',
        'sent_at',
        'received_at',
        'answered_at',
        'viewed_at',
        'user_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
        'answered_at' => 'datetime',
        'viewed_at' => 'datetime',
    ];

    /**
     * Relacionamento com ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Relacionamento com usuário que respondeu a mensagem
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Marcar mensagem como visualizada
     */
    public function marcarComoVisualizada(): bool
    {
        if (!$this->viewed_at) {
            return $this->update(['viewed_at' => now()]);
        }
        return false;
    }

    /**
     * Verifica se foi visualizada
     */
    public function foiVisualizada(): bool
    {
        return !is_null($this->viewed_at);
    }

    /**
     * Verifica se foi respondida
     */
    public function foiRespondida(): bool
    {
        return !is_null($this->answered_at);
    }
}
