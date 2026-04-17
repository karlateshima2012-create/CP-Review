<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transacao extends Model
{
    protected $table = 'transacoes';

    protected $fillable = [
        'transacao_id', 'empresa', 'email', 'telefone', 'line_id',
        'plano', 'valor', 'slug', 'pais', 'canal', 'cliente_id', 'status'
    ];
}
