<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transacao extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $table = 'transacoes';

    protected $fillable = [
        'transacao_id', 'empresa', 'email', 'telefone', 'line_id',
        'plano', 'valor', 'slug', 'pais', 'canal', 'tenant_id', 'status'
    ];
}
