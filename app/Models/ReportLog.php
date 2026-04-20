<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportLog extends Model
{
    use HasUuids;

    protected $fillable = ['tenant_id', 'periodo', 'status', 'opened_at'];

    protected $casts = [
        'opened_at' => 'datetime'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'tenant_id');
    }
}
