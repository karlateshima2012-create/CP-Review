<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BotScript extends Model
{
    use HasUuids;

    protected $fillable = ['tenant_id', 'locale', 'messages'];

    protected $casts = [
        'messages' => 'array'
    ];

    public function tenant()
    {
        return $this->belongsTo(Cliente::class, 'tenant_id');
    }
}
