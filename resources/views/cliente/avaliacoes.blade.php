@extends('layouts.cliente')

@section('title', 'Ocorrências - CP Review')

@section('cliente_content')
<!-- Page Header -->
<div class="mb-32">
    <h2 class="text-title-1 font-bold text-neutral-primary">{{ __('Ocorrências') }}</h2>
    <p class="text-body-m text-neutral-secondary">{{ __('Avaliações negativas — resolva antes que virem problema público') }}</p>
</div>

<!-- Filters Tab Bar -->
<div class="flex gap-8 mb-24 items-center overflow-x-auto pb-4 -mx-16 px-16 scroll-smooth">
    <!-- Todas -->
    <a href="{{ route('cliente.avaliacoes', [$cliente->id, 'filter' => 'todas']) }}" class="flex-shrink-0 px-12 py-8 rounded-full text-body-m font-medium transition {{ $filter === 'todas' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-neutral-secondary border border-neutral-border hover:bg-neutral-bg' }}">
        {{ __('Todas') }} ({{ $totalNegativas }})
    </a>
    <!-- Pendentes -->
    <a href="{{ route('cliente.avaliacoes', [$cliente->id, 'filter' => 'pendentes']) }}" class="flex-shrink-0 px-12 py-8 rounded-full text-body-m font-medium transition {{ $filter === 'pendentes' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-neutral-secondary border border-neutral-border hover:bg-neutral-bg' }}">
        {{ __('Pendentes') }} ({{ $negativasPendentes }})
    </a>
    <!-- Resolvidas -->
    <a href="{{ route('cliente.avaliacoes', [$cliente->id, 'filter' => 'resolvidas']) }}" class="flex-shrink-0 px-12 py-8 rounded-full text-body-m font-medium transition {{ $filter === 'resolvidas' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-neutral-secondary border border-neutral-border hover:bg-neutral-bg' }}">
        {{ __('Resolvidas') }} ({{ $negativasResolvidas }})
    </a>
    <!-- Com contato -->
    <a href="{{ route('cliente.avaliacoes', [$cliente->id, 'filter' => 'com_contato']) }}" class="flex-shrink-0 px-12 py-8 rounded-full text-body-m font-medium transition {{ $filter === 'com_contato' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-neutral-secondary border border-neutral-border hover:bg-neutral-bg' }}">
        {{ __('Com contato') }}
    </a>
    <!-- Sem contato -->
    <a href="{{ route('cliente.avaliacoes', [$cliente->id, 'filter' => 'sem_contato']) }}" class="flex-shrink-0 px-12 py-8 rounded-full text-body-m font-medium transition {{ $filter === 'sem_contato' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-neutral-secondary border border-neutral-border hover:bg-neutral-bg' }}">
        {{ __('Sem contato') }}
    </a>
</div>

<!-- Occurrences List -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
    @forelse($avaliacoes as $avaliacao)
        <div class="card overflow-hidden flex flex-col">
            <div class="p-16 flex flex-col flex-1">
                <!-- Header Card Info -->
                <div class="flex flex-wrap justify-between items-start gap-8 mb-8">
                    <div class="flex items-center gap-6">
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
                            <span class="bg-red-50 text-red-600 px-6 py-2 rounded text-legend font-semibold uppercase tracking-wider">{{ $avaliacao->problema }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-8">
                        <span class="text-legend text-neutral-secondary">{{ $avaliacao->created_at->format('d/m H:i') }}</span>
                        @if($avaliacao->resolvido)
                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-8 py-2 rounded text-legend font-bold uppercase tracking-wider">{{ __('Resolvida') }}</span>
                        @else
                            <span class="bg-amber-100 text-amber-800 px-8 py-2 rounded text-legend font-bold uppercase tracking-wider">{{ __('Pendente') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Feedback Text -->
                <p class="text-body-s text-neutral-secondary mb-8 leading-relaxed">
                    {{ $avaliacao->feedback ?: __('Sem feedback escrito') }}
                </p>

                <!-- Photo + Contact (inline) -->
                <div class="flex items-center gap-12 mb-8">
                    @if($avaliacao->foto_problema)
                        <a href="{{ Storage::url($avaliacao->foto_problema) }}" target="_blank" class="text-brand-600 hover:text-brand-700 text-legend font-medium inline-flex items-center gap-4">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path></svg>
                            {{ __('Ver foto') }}
                        </a>
                    @endif
                    @if($avaliacao->tipo_contato !== 'nao' && $avaliacao->contato_valor)
                        @if($avaliacao->tipo_contato === 'email')
                            <a href="mailto:{{ $avaliacao->contato_valor }}" class="text-brand-600 hover:underline text-legend font-semibold inline-flex items-center gap-4">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                {{ $avaliacao->contato_valor }}
                            </a>
                        @else
                            <a href="tel:{{ $avaliacao->contato_valor }}" class="text-brand-600 hover:underline text-legend font-semibold inline-flex items-center gap-4">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                                {{ $avaliacao->contato_valor }}
                            </a>
                        @endif
                    @endif
                </div>

                <!-- Internal Note / Actions Box -->
                @if($avaliacao->resolvido)
                    <div class="mt-auto pt-8 bg-emerald-50 border border-emerald-200 rounded-lg p-12">
                        <div class="flex justify-between items-start gap-8">
                            <div class="flex-1 min-w-0">
                                <p class="text-legend text-emerald-700 truncate">{{ $avaliacao->resposta_dono ?: __('Resolvido sem anotação.') }}</p>
                                @if($avaliacao->respondida_em)
                                    <p class="text-legend text-emerald-500 mt-2">{{ $avaliacao->respondida_em->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                            <button onclick="reabrirOcorrencia('{{ $avaliacao->id }}')" class="text-legend text-neutral-secondary hover:text-red-600 border border-neutral-border hover:border-red-300 px-8 py-4 rounded transition whitespace-nowrap flex-shrink-0">
                                Reabrir
                            </button>
                        </div>
                    </div>
                @else
                    <div class="mt-auto flex flex-col sm:flex-row gap-8 border-t border-neutral-border pt-12">
                        <button onclick="abrirResolverModal('{{ $avaliacao->id }}')" class="flex-1 border border-brand-600 text-brand-600 hover:bg-brand-50 px-12 py-8 rounded-lg text-legend font-bold transition text-center">
                            Anotação interna
                        </button>
                        <button onclick="marcarResolvidoDireto('{{ $avaliacao->id }}')" class="flex-1 bg-success-base text-white hover:bg-success-dark px-12 py-8 rounded-lg text-legend font-bold transition flex items-center justify-center gap-4">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                            Marcar resolvido
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="card p-48 text-center text-neutral-secondary lg:col-span-2">
            @if($filter === 'pendentes')
                Não há ocorrências pendentes.
            @else
                Nenhuma ocorrência encontrada para este filtro.
            @endif
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-24">
    {{ $avaliacoes->appends(['filter' => $filter])->links() }}
</div>

<!-- Modal Resolve / Anotação Interna -->
<div id="resolverModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-16 sm:p-24 max-w-md w-full mx-16 shadow-lg">
        <h2 class="text-title-3 font-bold mb-16 text-neutral-primary">Anotação Interna</h2>
        <p class="text-body-m text-neutral-secondary mb-12">Adicione uma anotação sobre como esta reclamação foi resolvida. Esta anotação serve apenas para controle interno.</p>
        <form id="resolverForm">
            @csrf
            <input type="hidden" id="modal_avaliacao_id">
            <textarea id="modal_anotacao" rows="4" class="w-full border border-neutral-border rounded-lg px-12 py-8 text-body-m mb-16 focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="Ex: Conversei com o cliente e oferecemos um desconto. Situação resolvida." required></textarea>
            <div class="flex gap-12">
                <button type="submit" class="flex-1 bg-brand-600 text-white py-12 rounded-lg font-bold hover:bg-brand-700 transition">
                    Salvar e Resolver
                </button>
                <button type="button" onclick="fecharResolverModal()" class="flex-1 bg-neutral-secondary/10 text-neutral-secondary py-12 rounded-lg font-bold hover:bg-neutral-secondary/20 transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const CSRF = '{{ csrf_token() }}';

    async function chamarResponder(id, body) {
        const res = await fetch(`/cliente/avaliacao/${id}/responder`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(body),
        });
        if (res.ok) {
            location.reload();
        } else {
            alert('Ocorreu um erro. Tente novamente.');
        }
    }

    function abrirResolverModal(id) {
        document.getElementById('modal_avaliacao_id').value = id;
        document.getElementById('modal_anotacao').value = '';
        document.getElementById('resolverModal').classList.remove('hidden');
        document.getElementById('resolverModal').classList.add('flex');
    }

    function fecharResolverModal() {
        document.getElementById('resolverModal').classList.add('hidden');
        document.getElementById('resolverModal').classList.remove('flex');
    }

    function marcarResolvidoDireto(id) {
        chamarResponder(id, { resposta: null });
    }

    function reabrirOcorrencia(id) {
        if (!confirm('Reabrir esta ocorrência?')) return;
        chamarResponder(id, { reabrir: true });
    }

    document.getElementById('resolverForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const id = document.getElementById('modal_avaliacao_id').value;
        const resposta = document.getElementById('modal_anotacao').value.trim();
        fecharResolverModal();
        chamarResponder(id, { resposta });
    });
</script>
@endsection
