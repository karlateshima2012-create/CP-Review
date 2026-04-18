<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class DailyMetricsSummary extends Model
{
    use BelongsToTenant;

    protected $table = 'daily_metrics_summary';
    
    // Como a chave é composta e não auto-incremento
    public $incrementing = false;
    protected $primaryKey = ['tenant_id', 'metric_date', 'period'];

    protected $fillable = [
        'tenant_id',
        'metric_date',
        'period',
        'total_reviews',
        'positive_count',
        'negative_count',
        'rating_1',
        'rating_2',
        'rating_3',
        'rating_4',
        'rating_5',
        'avg_rating',
        'first_visit_count',
        'returning_count',
        'top_issues',
        'aggregated_at'
    ];

    protected $casts = [
        'metric_date' => 'date',
        'top_issues' => 'array',
        'aggregated_at' => 'datetime',
        'avg_rating' => 'float'
    ];

    public $timestamps = false; // Usamos aggregated_at manualmente

    public function tenant()
    {
        return $this->belongsTo(Cliente::class, 'tenant_id');
    }
}
