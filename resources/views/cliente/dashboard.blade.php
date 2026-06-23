@extends('layouts.cliente')

@section('title', 'Dashboard - CP Review')

@section('cliente_content')

<!-- Page Header -->
<div class="mb-24">
    <h2 class="text-title-1 font-bold text-neutral-primary">{{ __('Dashboard') }}</h2>
    <p class="text-body-m text-neutral-secondary">{{ $cliente->nome_empresa }}</p>
</div>

<!-- ── KPI CARDS ──────────────────────────────────────────────────────────── -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-16 mb-24">

    {{-- Acessos ao Bot --}}
    <div class="card p-16 flex flex-col gap-4">
        <span class="text-legend font-bold uppercase tracking-wider text-neutral-secondary">{{ __('Acessos ao Bot') }}</span>
        <span class="text-[32px] font-bold text-neutral-primary leading-none">{{ number_format($totalScans) }}</span>
        <span class="text-legend text-neutral-secondary">{{ __('QR codes escaneados') }}</span>
    </div>

    {{-- Avaliações Positivas --}}
    <div class="card p-16 flex flex-col gap-4">
        <span class="text-legend font-bold uppercase tracking-wider text-neutral-secondary">{{ __('Positivas') }}</span>
        <span class="text-[32px] font-bold text-emerald-600 leading-none">{{ number_format($positivas) }}</span>
        <span class="text-legend text-neutral-secondary">{{ __('Notas 4★ e 5★') }}</span>
    </div>

    {{-- Avaliações Negativas --}}
    <div class="card p-16 flex flex-col gap-4">
        <span class="text-legend font-bold uppercase tracking-wider text-neutral-secondary">{{ __('Negativas') }}</span>
        <span class="text-[32px] font-bold text-red-500 leading-none">{{ number_format($negativas) }}</span>
        <div class="flex items-center justify-between flex-wrap gap-4">
            <span class="text-legend text-neutral-secondary">{{ __('Notas 1★ a 3★') }}</span>
            @if($ocorrenciasPendentes->count() > 0)
                <span class="text-legend bg-amber-100 text-amber-700 font-bold px-8 py-2 rounded whitespace-nowrap">{{ $ocorrenciasPendentes->count() }} pendentes</span>
            @endif
        </div>
    </div>

    {{-- Sem Avaliação --}}
    <div class="card p-16 flex flex-col gap-4">
        <span class="text-legend font-bold uppercase tracking-wider text-neutral-secondary">{{ __('Sem Avaliação') }}</span>
        <span class="text-[32px] font-bold text-amber-500 leading-none">{{ number_format($semAvaliacao) }}</span>
        <div class="flex items-center justify-between flex-wrap gap-4">
            <span class="text-legend text-neutral-secondary">{{ __('Saíram sem avaliar') }}</span>
            <span class="text-legend text-neutral-secondary font-bold whitespace-nowrap">{{ $taxaConversao }}% {{ __('conversão') }}</span>
        </div>
    </div>

</div>

<!-- ── SCORE CARD ──────────────────────────────────────────────────────────── -->
<div class="card p-24 mb-24 grid md:grid-cols-3 gap-24 items-center">
    {{-- Média --}}
    <div class="flex flex-col items-center md:items-start text-center md:text-left md:border-r border-neutral-border md:pr-24 pb-16 md:pb-0">
        <span class="text-[56px] font-bold text-neutral-primary leading-none">{{ number_format($mediaNotas, 1) }}</span>
        <div class="flex gap-4 my-8 text-amber-400">
            @for($i = 1; $i <= 5; $i++)
                @if($i <= round($mediaNotas))
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @else
                    <svg class="w-16 h-16 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endif
            @endfor
        </div>
        <span class="text-body-m text-neutral-secondary">{{ $totalAvaliacoes }} {{ __('avaliações no total') }}</span>
    </div>

    {{-- Barras por estrela --}}
    <div class="space-y-8 md:border-r border-neutral-border md:pr-24 border-t md:border-t-0 pt-16 md:pt-0">
        @foreach([5 => 'bg-emerald-500', 4 => 'bg-emerald-400', 3 => 'bg-amber-400', 2 => 'bg-orange-400', 1 => 'bg-red-500'] as $star => $colorClass)
            @php $pct = $totalAvaliacoes > 0 ? ($starCounts[$star] / $totalAvaliacoes) * 100 : 0; @endphp
            <div class="flex items-center gap-12 text-body-m text-neutral-secondary">
                <span class="w-8 text-right font-medium">{{ $star }}</span>
                <div class="flex-1 bg-gray-100 rounded-full h-8 overflow-hidden">
                    <div class="{{ $colorClass }} h-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
                <span class="w-24 text-right">{{ $starCounts[$star] }}</span>
            </div>
        @endforeach
    </div>

    {{-- Totais --}}
    <div class="flex flex-col gap-12 md:flex-col md:justify-center md:gap-16 pl-12 text-center md:text-left border-t md:border-t-0 border-neutral-border pt-16 md:pt-0">
        <div>
            <span class="block text-title-1 font-bold text-brand-600 leading-none">{{ $totalAvaliacoes }}</span>
            <span class="text-legend text-neutral-secondary uppercase font-bold tracking-wider">{{ __('Total') }}</span>
        </div>
        <div>
            <span class="block text-title-1 font-bold text-emerald-600 leading-none">{{ $positivas }}</span>
            <span class="text-legend text-neutral-secondary uppercase font-bold tracking-wider">{{ __('Positivas') }} (4-5★)</span>
        </div>
        <div>
            <span class="block text-title-1 font-bold text-red-500 leading-none">{{ $negativas }}</span>
            <span class="text-legend text-neutral-secondary uppercase font-bold tracking-wider">{{ __('Negativas') }} (1-3★)</span>
        </div>
    </div>
</div>

<!-- ── HISTÓRICO COMPLETO ─────────────────────────────────────────────────── -->
<div class="card overflow-hidden mb-24">
    <div class="p-16 flex flex-wrap justify-between items-center gap-8 border-b border-neutral-border bg-neutral-card">
        <div>
            <h3 class="text-body-g font-bold text-neutral-primary">{{ __('Histórico de Avaliações') }}</h3>
            <p class="text-legend text-neutral-secondary mt-2">{{ __('Últimas :count avaliações recebidas', ['count' => $historicoRecente->count()]) }}</p>
        </div>
        <a href="{{ route('cliente.avaliacoes', $cliente->id) }}" class="border border-neutral-border hover:bg-neutral-bg px-12 py-8 rounded-lg text-body-m font-medium text-neutral-secondary transition">
            {{ __('Ver todas →') }}
        </a>
    </div>

    @if($historicoRecente->isEmpty())
        <div class="p-32 text-center text-neutral-secondary">
            <p class="text-title-1 mb-8">📋</p>
            <p class="text-body-m font-medium">{{ __('Nenhuma avaliação recebida ainda.') }}</p>
            <p class="text-legend mt-4">{{ __('Compartilhe o QR Code com seus clientes para começar a receber feedback.') }}</p>
        </div>
    @else
        <div class="flex flex-col divide-y divide-neutral-border">
            @foreach($historicoRecente as $avaliacao)
                @php
                    $isPositiva = $avaliacao->nota >= 4;
                    $rowBg = $isPositiva ? 'bg-neutral-card' : 'bg-red-50/20';
                    $hoverBg = $isPositiva ? 'hover:bg-neutral-bg' : 'hover:bg-red-50/40';
                @endphp
                <div class="p-16 md:p-24 flex items-start gap-16 {{ $rowBg }} {{ $hoverBg }} transition">

                    {{-- Nota / Estrelas --}}
                    <div class="flex-shrink-0 text-center w-48 flex flex-col items-center">
                        <span class="block text-title-2 font-bold {{ $isPositiva ? 'text-emerald-600' : 'text-red-500' }} leading-none">
                            {{ $avaliacao->nota }}
                        </span>
                        <div class="flex justify-center text-amber-400 text-[10px] leading-none mt-4 gap-[2px]">
                            @for($i = 1; $i <= $avaliacao->nota; $i++)
                                <span>★</span>
                            @endfor
                        </div>
                    </div>

                    {{-- Conteúdo --}}
                    <div class="flex-1 min-w-0 flex flex-col gap-8">
                        {{-- Topo: Badges e Data --}}
                        <div class="flex flex-wrap items-center justify-between gap-8">
                            <div class="flex items-center gap-8 flex-wrap">
                                @if($isPositiva)
                                    <span class="text-legend bg-emerald-50 text-emerald-700 font-bold px-6 py-2 rounded uppercase tracking-wider border border-emerald-200">{{ __('Positiva') }}</span>
                                @else
                                    <span class="text-legend {{ $avaliacao->resolvido ? 'bg-gray-100 text-gray-500 border-gray-200' : 'bg-red-50 text-red-600 border-red-200' }} font-bold px-6 py-2 rounded uppercase tracking-wider border">
                                        {{ $avaliacao->resolvido ? __('Resolvida') : __('Pendente') }}
                                    </span>
                                @endif

                                @if($avaliacao->problema)
                                    <span class="inline-flex items-center text-[10px] bg-neutral-bg border border-neutral-border text-neutral-secondary px-6 py-1 rounded font-medium">
                                        {{ $avaliacao->problema }}
                                    </span>
                                @endif
                            </div>

                            <div class="text-right text-legend text-neutral-secondary whitespace-nowrap">
                                <span>{{ $avaliacao->created_at->format('d/m/Y') }}</span>
                                <span class="text-neutral-secondary/40 mx-4">•</span>
                                <span>{{ $avaliacao->created_at->format('H:i') }}</span>
                            </div>
                        </div>

                        {{-- Comentário --}}
                        <div class="text-body-m text-neutral-primary leading-relaxed break-words">
                            @if($avaliacao->feedback)
                                {{ $avaliacao->feedback }}
                            @else
                                <span class="text-neutral-secondary/40 italic">{{ __('Sem comentário escrito') }}</span>
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- ── OCORRÊNCIAS PENDENTES (alerta) ─────────────────────────────────────── -->
@if($ocorrenciasPendentes->count() > 0)
<div class="card overflow-hidden mb-24">
    <div class="p-16 flex flex-wrap justify-between items-center gap-8 border-b border-neutral-border bg-amber-50">
        <div class="flex items-center gap-12">
            <div class="w-32 h-32 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-body-g font-bold text-neutral-primary">{{ __('Ocorrências pendentes') }}</h3>
                <p class="text-body-m text-neutral-secondary">{{ __(':count avaliação(ões) negativa(s) aguardando sua resposta', ['count' => $ocorrenciasPendentes->count()]) }}</p>
            </div>
        </div>
        <a href="{{ route('cliente.avaliacoes', $cliente->id) }}" class="border border-neutral-border bg-white hover:bg-neutral-bg px-12 py-8 rounded-lg text-body-m font-medium text-neutral-secondary transition">
            {{ __('Responder →') }}
        </a>
    </div>
    <div class="divide-y divide-amber-100/60 bg-amber-50/30">
        @foreach($ocorrenciasPendentes->take(3) as $avaliacao)
            <div class="p-16 flex items-start gap-12">
                <div class="flex gap-2 text-amber-400 flex-shrink-0 mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $avaliacao->nota)
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @else
                            <svg class="w-12 h-12 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endif
                    @endfor
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-body-m text-neutral-primary truncate">{{ $avaliacao->feedback ?: __('Sem comentário escrito') }}</p>
                    <p class="text-legend text-neutral-secondary mt-2">{{ $avaliacao->created_at->diffForHumans() }}</p>
                </div>
            </div>
        @endforeach
        @if($ocorrenciasPendentes->count() > 3)
            <div class="p-12 text-center">
                <a href="{{ route('cliente.avaliacoes', $cliente->id) }}" class="text-body-m text-brand-600 font-semibold hover:underline">
                    {{ __('Ver mais :count ocorrências →', ['count' => $ocorrenciasPendentes->count() - 3]) }}
                </a>
            </div>
        @endif
    </div>
</div>
@endif
@endsection
