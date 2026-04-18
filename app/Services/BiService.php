<?php

namespace App\Services;

use App\Models\DailyMetricsSummary;
use Illuminate\Support\Collection;

class BiService
{
    /**
     * Retorna dados para o Heatmap (dias × períodos).
     * 
     * Query extremamente rápida pois lê da tabela pré-agregada.
     */
    public function getHeatmapData(string $tenantId, int $days = 30): Collection
    {
        return DailyMetricsSummary::where('tenant_id', $tenantId)
            ->where('metric_date', '>=', now()->subDays($days))
            ->where('period', '!=', 'all')
            ->orderBy('metric_date')
            ->get(['metric_date', 'period', 'avg_rating', 'negative_count', 'total_reviews'])
            ->map(fn($m) => [
                'date' => $m->metric_date->toDateString(),
                'period' => $m->period,
                'avg' => (float) $m->avg_rating,
                'total' => $m->total_reviews,
                'negative' => $m->negative_count,
                // Intensidade para o heatmap (0 a 1)
                'intensity' => $m->total_reviews > 0 
                    ? round($m->negative_count / $m->total_reviews, 2) 
                    : 0,
            ]);
    }

    /**
     * Resumo executivo do mês para o dashboard.
     */
    public function getMonthlySummary(string $tenantId): array
    {
        $rows = DailyMetricsSummary::where('tenant_id', $tenantId)
            ->where('period', 'all')
            ->where('metric_date', '>=', now()->startOfMonth())
            ->get();

        return [
            'total' => $rows->sum('total_reviews'),
            'positive' => $rows->sum('positive_count'),
            'negative' => $rows->sum('negative_count'),
            'avg_rating' => $rows->count() > 0 ? round($rows->avg('avg_rating'), 2) : 0,
            'new_clients' => $rows->sum('first_visit_count'),
        ];
    }
}
