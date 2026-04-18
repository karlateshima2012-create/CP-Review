<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avaliacao extends Model
{
    use HasUuids, HasFactory, \App\Traits\BelongsToTenant;

    protected $table = 'avaliacoes';

    protected $fillable = [
        'tenant_id', 'nota', 'feedback', 'problema', 'nome_cliente',
        'tipo_contato', 'contato_valor', 'token_resposta',
        'resposta_dono', 'respondida_em', 'resolvido',
        'primeira_visita', 'periodo_visita', 'foto_problema'
    ];

    protected $casts = [
        'resolvido' => 'boolean',
        'respondida_em' => 'datetime'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'tenant_id');
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
