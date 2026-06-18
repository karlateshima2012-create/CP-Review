@extends('layouts.cliente')

@section('title', 'Dashboard - CP Review')

@php
    $total = $totalAvaliacoes ?: 1;
    $star5 = $cliente->avaliacoes()->where('nota', 5)->count();
    $star4 = $cliente->avaliacoes()->where('nota', 4)->count();
    $star3 = $cliente->avaliacoes()->where('nota', 3)->count();
    $star2 = $cliente->avaliacoes()->where('nota', 2)->count();
    $star1 = $cliente->avaliacoes()->where('nota', 1)->count();
    
    $pct5 = ($star5 / $total) * 100;
    $pct4 = ($star4 / $total) * 100;
    $pct3 = ($star3 / $total) * 100;
    $pct2 = ($star2 / $total) * 100;
    $pct1 = ($star1 / $total) * 100;

    $positivas = $cliente->avaliacoes()->whereIn('nota', [4, 5])->count();
    $negativasCount = $cliente->avaliacoes()->whereIn('nota', [1, 2, 3])->count();
    
    $ocorrenciasPendentes = $cliente->avaliacoes()->where('nota', '<=', 3)->where('resolvido', false)->orderBy('created_at', 'desc')->get();
@endphp

@section('cliente_content')
<!-- Page Header -->
<div class="mb-32">
    <h2 class="text-title-1 font-bold text-neutral-primary">Dashboard</h2>
    <p class="text-body-m text-neutral-secondary">{{ $cliente->nome_empresa }}</p>
</div>

<!-- Score Card -->
<div class="card p-24 mb-24 grid md:grid-cols-3 gap-24 items-center">
    <!-- Left: Avg rating -->
    <div class="flex flex-col items-center md:items-start text-center md:text-left md:border-r border-neutral-border md:pr-24">
        <span class="text-[64px] font-bold text-neutral-primary leading-none">{{ number_format($mediaNotas, 1) }}</span>
        <!-- Star rating display -->
        <div class="flex gap-4 my-8 text-amber-400">
            @for($i = 1; $i <= 5; $i++)
                @if($i <= round($mediaNotas))
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @else
                    <svg class="w-16 h-16 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endif
            @endfor
        </div>
        <span class="text-body-m text-neutral-secondary">{{ $totalAvaliacoes }} avaliações</span>
    </div>

    <!-- Center: Stars bars -->
    <div class="space-y-8 md:border-r border-neutral-border md:pr-24">
        @foreach([5 => 'bg-[#10B981]', 4 => 'bg-[#34D399]', 3 => 'bg-[#F59E0B]', 2 => 'bg-[#F97316]', 1 => 'bg-[#EF4444]'] as $star => $colorClass)
            @php
                $count = ${"star" . $star};
                $pct = $totalAvaliacoes > 0 ? ($count / $totalAvaliacoes) * 100 : 0;
            @endphp
            <div class="flex items-center gap-12 text-body-m text-neutral-secondary">
                <span class="w-8 text-right font-medium">{{ $star }}</span>
                <div class="flex-1 bg-gray-100 rounded-full h-8 overflow-hidden">
                    <div class="{{ $colorClass }} h-full" style="width: {{ $pct }}%"></div>
                </div>
                <span class="w-24 text-right">{{ $count }}</span>
            </div>
        @endforeach
    </div>

    <!-- Right: positive vs negative -->
    <div class="flex justify-around md:flex-col md:justify-center md:gap-16 pl-12 text-center md:text-left">
        <div>
            <span class="block text-title-1 font-bold text-brand-600 leading-none">{{ $totalAvaliacoes }}</span>
            <span class="text-legend text-neutral-secondary uppercase font-bold tracking-wider">Total</span>
        </div>
        <div>
            <span class="block text-title-1 font-bold text-success-base leading-none">{{ $positivas }}</span>
            <span class="text-legend text-neutral-secondary uppercase font-bold tracking-wider">Positivas (4-5★)</span>
        </div>
        <div>
            <span class="block text-title-1 font-bold text-error-base leading-none">{{ $negativasCount }}</span>
            <span class="text-legend text-neutral-secondary uppercase font-bold tracking-wider">Negativas (1-3★)</span>
        </div>
    </div>
</div>

<!-- Pending Occurrences Card -->
<div class="card overflow-hidden">
    <!-- Header -->
    <div class="p-16 flex justify-between items-center border-b border-neutral-border bg-neutral-card">
        <div class="flex items-center gap-12">
            <div class="w-32 h-32 bg-red-50 text-red-600 rounded-lg flex items-center justify-center">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-body-g font-bold text-neutral-primary">Ocorrências pendentes</h3>
                <p class="text-body-m text-neutral-secondary">{{ $ocorrenciasPendentes->count() }} aguardando resolução</p>
            </div>
        </div>
        <a href="{{ route('cliente.avaliacoes', $cliente->id) }}" class="border border-neutral-border hover:bg-neutral-bg px-12 py-8 rounded-lg text-body-m font-medium text-neutral-secondary transition">
            Ver todas →
        </a>
    </div>

    <!-- Occurrences List -->
    <div class="bg-[#FFFBEB]/40 divide-y divide-amber-100/60">
        @forelse($ocorrenciasPendentes as $avaliacao)
            <div class="p-16">
                <!-- Top Row -->
                <div class="flex justify-between items-start mb-8">
                    <!-- Stars and Pill Category -->
                    <div class="flex items-center gap-8">
                        <div class="flex text-amber-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $avaliacao->nota)
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @else
                                    <svg class="w-12 h-12 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                        </div>
                        @if($avaliacao->problema)
                            <span class="bg-red-50 text-red-600 px-8 py-4 rounded text-legend font-semibold uppercase tracking-wider">
                                {{ $avaliacao->problema }}
                            </span>
                        @endif
                    </div>
                    <!-- Date and status -->
                    <div class="flex items-center gap-8">
                        <span class="text-body-m text-neutral-secondary">{{ $avaliacao->created_at->format('d/m H:i') }}</span>
                        <span class="bg-amber-100 text-amber-800 px-8 py-4 rounded text-legend font-bold uppercase tracking-wider">Pendente</span>
                    </div>
                </div>

                <!-- Comment text -->
                <p class="text-body-m text-neutral-secondary mb-8 leading-relaxed">{{ $avaliacao->feedback ?: 'Sem feedback escrito' }}</p>

                <!-- Phone -->
                @if($avaliacao->tipo_contato !== 'nao' && $avaliacao->contato_valor)
                    <div class="flex items-center gap-4 text-body-m text-brand-600 font-medium">
                        @if($avaliacao->tipo_contato === 'email')
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                            <a href="mailto:{{ $avaliacao->contato_valor }}" class="hover:underline">{{ $avaliacao->contato_valor }}</a>
                        @else
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                            <a href="tel:{{ $avaliacao->contato_valor }}" class="hover:underline">{{ $avaliacao->contato_valor }}</a>
                        @endif
                    </div>
                @else
                    <p class="text-legend text-neutral-secondary/50 italic font-medium">Sem contato deixado</p>
                @endif
            </div>
        @empty
            <div class="p-24 text-center text-neutral-secondary bg-neutral-card">
                🎉 Nenhuma ocorrência pendente! Bom trabalho.
            </div>
        @endforelse
    </div>
</div>
@endsection
