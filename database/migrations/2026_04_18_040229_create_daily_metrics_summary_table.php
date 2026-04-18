<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_metrics_summary', function (Blueprint $table) {
            // Chave composta: tenant + data + período
            $table->uuid('tenant_id');
            $table->date('metric_date');
            $table->enum('period', ['lunch', 'dinner', 'other', 'all']);

            // Contagens por faixa de nota
            $table->unsignedSmallInteger('total_reviews')->default(0);
            $table->unsignedSmallInteger('positive_count')->default(0); // 4-5
            $table->unsignedSmallInteger('negative_count')->default(0); // 1-3
            $table->unsignedSmallInteger('rating_1')->default(0);
            $table->unsignedSmallInteger('rating_2')->default(0);
            $table->unsignedSmallInteger('rating_3')->default(0);
            $table->unsignedSmallInteger('rating_4')->default(0);
            $table->unsignedSmallInteger('rating_5')->default(0);

            // Média pré-calculada
            $table->decimal('avg_rating', 3, 2)->default(0);

            // Contexto de clientes
            $table->unsignedSmallInteger('first_visit_count')->default(0);
            $table->unsignedSmallInteger('returning_count')->default(0);

            // Top issues do dia (JSON)
            $table->json('top_issues')->nullable();

            $table->timestamp('aggregated_at')->useCurrent();

            // Chave primária composta para evitar duplicidade
            $table->primary(['tenant_id', 'metric_date', 'period']);

            // Índice para range de datas
            $table->index(['tenant_id', 'metric_date'], 'idx_tenant_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_metrics_summary');
    }
};
