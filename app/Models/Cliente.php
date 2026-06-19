<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'nome_empresa', 'email', 'telefone_whatsapp', 'line_user_id',
        'slug', 'google_maps_link', 'pais', 'canal_notificacao', 'plano', 'ativo', 'data_ativacao',
        'valor_mensal', 'trial_ends_at', 'status',
        'msg_boas_vindas_br', 'msg_pergunta_nota_br', 'msg_agradecimento_alta_br', 'msg_agradecimento_baixa_br',
        'msg_boas_vindas_jp', 'msg_pergunta_nota_jp', 'msg_agradecimento_alta_jp', 'msg_agradecimento_baixa_jp',
        'logo_path', 'cover_path', 'motivos_problema'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_ativacao' => 'datetime',
        'trial_ends_at' => 'datetime',
        'valor_mensal' => 'decimal:2',
        'motivos_problema' => 'array'
    ];

    public function inTrial(): bool
    {
        return $this->status === 'trial' || ($this->trial_ends_at && $this->trial_ends_at->isFuture());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function avaliacoes(): HasMany
    {
        // Fallback defensivo para evitar crash durante a migração de cliente_id -> tenant_id
        $foreignKey = \Illuminate\Support\Facades\Schema::hasTable('avaliacoes') && 
                      \Illuminate\Support\Facades\Schema::hasColumn('avaliacoes', 'tenant_id') 
                      ? 'tenant_id' : 'cliente_id';
                      
        return $this->hasMany(Avaliacao::class, $foreignKey);
    }

    public function getUrlAvaliacaoAttribute(): string
    {
        return route('avaliacao.show', ['slug' => $this->slug]);
    }

    public function getQrCodeUrlAttribute(): string
    {
        return url("/cliente/qrcode/{$this->id}");
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_path ? asset('storage/' . $this->cover_path) : null;
    }
}
