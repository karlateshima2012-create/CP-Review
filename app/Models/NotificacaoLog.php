<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificacaoLog extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'notificacoes_logs';

    protected $fillable = [
        'tenant_id', 'avaliacao_id', 'canal', 'destinatario',
        'mensagem', 'status', 'erro_mensagem', 'retries'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'tenant_id');
    }

    public function avaliacao(): BelongsTo
    {
        return $this->belongsTo(Avaliacao::class, 'avaliacao_id');
    }
}
