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
        'msg_boas_vindas_br', 'msg_pergunta_nota_br', 'msg_agradecimento_alta_br', 'msg_agradecimento_baixa_br',
        'msg_boas_vindas_jp', 'msg_pergunta_nota_jp', 'msg_agradecimento_alta_jp', 'msg_agradecimento_baixa_jp'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_ativacao' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }

    public function getUrlAvaliacaoAttribute(): string
    {
        return url("/avaliacao/{$this->slug}");
    }

    public function getQrCodeUrlAttribute(): string
    {
        return url("/cliente/qrcode/{$this->id}");
    }
}
