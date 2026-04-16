<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avaliacao extends Model
{
    use HasUuids;

    protected $fillable = [
        'cliente_id', 'nota', 'feedback', 'problema', 'nome_cliente',
        'tipo_contato', 'contato_valor', 'token_resposta',
        'resposta_dono', 'respondida_em', 'resolvido',
        'primeira_visita', 'periodo_visita', 'foto_problema'
    ];

    protected $casts = [
        'resolvido' => 'boolean',
        'respondida_em' => 'datetime'
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function isNegativa(): bool
    {
        return $this->nota <= 3;
    }

    public function getStarsAttribute(): string
    {
        return str_repeat('⭐', $this->nota);
    }
}
