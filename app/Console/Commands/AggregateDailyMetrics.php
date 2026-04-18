<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;
use App\Models\Avaliacao;
use App\Models\DailyMetricsSummary;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AggregateDailyMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:aggregate {--date= : Data no formato Y-m-d (padrão: ontem)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agrega métricas diárias de avaliações na tabela daily_metrics_summary';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::yesterday();

        $this->info("Agregando métricas para {$date->toDateString()}...");

        // Processa todos os lojistas ativos
        Cliente::where('ativo', true)->chunkById(50, function ($tenants) use ($date) {
            foreach ($tenants as $tenant) {
                $this->aggregateTenant($tenant, $date);
            }
        });

        $this->info('Agregação concluída com sucesso!');
        return Command::SUCCESS;
    }

    private function aggregateTenant(Cliente $tenant, Carbon $date): void
    {
        // Períodos para agregar (os específicos + o global 'all')
        $periods = ['lunch', 'dinner', 'other', 'all'];

        foreach ($periods as $period) {
            // Buscamos as avaliações sem o escopo global (estamos processando cross-tenant aqui)
            $query = Avaliacao::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->whereDate('created_at', $date);

            if ($period !== 'all') {
                $query->where('periodo_visita', $period);
            }

            $reviews = $query->get(['nota', 'primeira_visita', 'problema']);

            if ($reviews->isEmpty()) continue;

            // Calcula principais problemas (top issues)
            $topIssues = $reviews
                ->whereNotNull('problema')
                ->where('problema', '!=', '')
                ->groupBy('problema')
                ->map(fn($g) => ['category' => $g->first()->problema, 'count' => $g->count()])
                ->sortByDesc('count')
                ->take(3)
                ->values();

            // Salva ou atualiza (upsert)
            DailyMetricsSummary::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'metric_date' => $date->toDateString(),
                    'period' => $period,
                ],
                [
                    'total_reviews' => $reviews->count(),
                    'positive_count' => $reviews->where('nota', '>=', 4)->count(),
                    'negative_count' => $reviews->where('nota', '<=', 3)->count(),
                    'rating_1' => $reviews->where('nota', 1)->count(),
                    'rating_2' => $reviews->where('nota', 2)->count(),
                    'rating_3' => $reviews->where('nota', 3)->count(),
                    'rating_4' => $reviews->where('nota', 4)->count(),
                    'rating_5' => $reviews->where('nota', 5)->count(),
                    'avg_rating' => round($reviews->avg('nota'), 2),
                    'first_visit_count' => $reviews->where('primeira_visita', true)->count(),
                    'returning_count' => $reviews->where('primeira_visita', false)->count(),
                    'top_issues' => $topIssues->toArray(),
                    'aggregated_at' => now(),
                ]
            );
        }
    }
}
