@extends('layouts.cliente')

@section('title', 'Ocorrências - CP Review')

@section('cliente_content')
<!-- Page Header -->
<div class="mb-32">
    <h2 class="text-title-1 font-bold text-neutral-primary">Ocorrências</h2>
    <p class="text-body-m text-neutral-secondary">Avaliações negativas — resolva antes que virem problema público</p>
</div>

<!-- Filters Tab Bar -->
<div class="flex flex-wrap gap-8 mb-24 items-center">
    <!-- Todas -->
    <a href="{{ route('cliente.avaliacoes', [$cliente->id, 'filter' => 'todas']) }}" class="px-12 py-8 rounded-full text-body-m font-medium transition {{ $filter === 'todas' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-neutral-secondary border border-neutral-border hover:bg-neutral-bg' }}">
        Todas ({{ $totalNegativas }})
    </a>
    <!-- Pendentes -->
    <a href="{{ route('cliente.avaliacoes', [$cliente->id, 'filter' => 'pendentes']) }}" class="px-12 py-8 rounded-full text-body-m font-medium transition {{ $filter === 'pendentes' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-neutral-secondary border border-neutral-border hover:bg-neutral-bg' }}">
        Pendentes ({{ $negativasPendentes }})
    </a>
    <!-- Resolvidas -->
    <a href="{{ route('cliente.avaliacoes', [$cliente->id, 'filter' => 'resolvidas']) }}" class="px-12 py-8 rounded-full text-body-m font-medium transition {{ $filter === 'resolvidas' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-neutral-secondary border border-neutral-border hover:bg-neutral-bg' }}">
        Resolvidas ({{ $negativasResolvidas }})
    </a>
    <!-- Com contato -->
    <a href="{{ route('cliente.avaliacoes', [$cliente->id, 'filter' => 'com_contato']) }}" class="px-12 py-8 rounded-full text-body-m font-medium transition {{ $filter === 'com_contato' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-neutral-secondary border border-neutral-border hover:bg-neutral-bg' }}">
        Com contato
    </a>
    <!-- Sem contato -->
    <a href="{{ route('cliente.avaliacoes', [$cliente->id, 'filter' => 'sem_contato']) }}" class="px-12 py-8 rounded-full text-body-m font-medium transition {{ $filter === 'sem_contato' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-neutral-secondary border border-neutral-border hover:bg-neutral-bg' }}">
        Sem contato
    </a>
</div>

<!-- Occurrences List -->
<div class="space-y-16">
    @forelse($avaliacoes as $avaliacao)
        <div class="card overflow-hidden">
            <div class="p-24">
                <!-- Header Card Info -->
                <div class="flex justify-between items-start mb-16">
                    <!-- Stars & Problem tag -->
                    <div class="flex items-center gap-8">
                        <div class="flex text-amber-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $avaliacao->nota)
                                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @else
                                    <svg class="w-16 h-16 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                        </div>
                        @if($avaliacao->problema)
                            <span class="bg-red-50 text-red-600 px-8 py-4 rounded text-legend font-semibold uppercase tracking-wider">
                                {{ $avaliacao->problema }}
                            </span>
                        @endif
                    </div>
                    <!-- Date & Status Badge -->
                    <div class="flex items-center gap-12">
                        <span class="text-body-m text-neutral-secondary">{{ $avaliacao->created_at->format('d/m H:i') }}</span>
                        @if($avaliacao->resolvido)
                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-12 py-4 rounded text-legend font-bold uppercase tracking-wider">Resolvido</span>
                        @else
                            <span class="bg-amber-100 text-amber-800 px-12 py-4 rounded text-legend font-bold uppercase tracking-wider">Pendente</span>
                        @endif
                    </div>
                </div>

                <!-- Feedback Text -->
                <p class="text-body-m text-neutral-secondary mb-16 leading-relaxed">
                    {{ $avaliacao->feedback ?: 'Sem feedback escrito' }}
                </p>

                <!-- Photo attachment -->
                @if($avaliacao->foto_problema)
                    <div class="mb-16">
                        <a href="{{ Storage::url($avaliacao->foto_problema) }}" target="_blank" class="text-brand-600 hover:text-brand-700 text-body-m font-medium inline-flex items-center gap-4">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path>
                            </svg>
                            Ver Foto em Anexo
                        </a>
                    </div>
                @endif

                <!-- Contact -->
                @if($avaliacao->tipo_contato !== 'nao' && $avaliacao->contato_valor)
                    <div class="flex items-center gap-4 text-body-m text-brand-600 font-semibold mb-16">
                        @if($avaliacao->tipo_contato === 'email')
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                            <a href="mailto:{{ $avaliacao->contato_valor }}" class="hover:underline">{{ $avaliacao->contato_valor }}</a>
                        @else
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                            <a href="tel:{{ $avaliacao->contato_valor }}" class="hover:underline">{{ $avaliacao->contato_valor }}</a>
                        @endif
                    </div>
                @else
                    <p class="text-legend text-neutral-secondary/50 italic font-medium mb-16">Sem contato deixado</p>
                @endif

                <!-- Internal Note / Actions Box -->
                @if($avaliacao->resolvido)
                    <div class="mt-12 bg-emerald-50 border border-emerald-200 rounded-lg p-16">
                        <h4 class="text-body-m font-bold text-emerald-800 mb-4">Anotação Interna</h4>
                        <p class="text-body-m text-emerald-700">{{ $avaliacao->resposta_dono ?: 'Resolvido sem anotação.' }}</p>
                    </div>
                @else
                    <div class="mt-16 flex gap-12 border-t border-neutral-border pt-16">
                        <button onclick="abrirResolverModal({{ $avaliacao->id }})" class="border border-brand-600 text-brand-600 hover:bg-brand-50 px-16 py-8 rounded-lg text-body-m font-bold transition flex items-center gap-4">
                            Anotação interna
                        </button>
                        <button onclick="marcarResolvidoDireto({{ $avaliacao->id }})" class="bg-success-base text-white hover:bg-success-dark px-16 py-8 rounded-lg text-body-m font-bold transition flex items-center gap-4">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                            Marcar resolvido
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="card p-48 text-center text-neutral-secondary">
            Nenhuma ocorrência encontrada para este filtro.
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-24">
    {{ $avaliacoes->appends(['filter' => $filter])->links() }}
</div>

<!-- Modal Resolve / Anotação Interna -->
<div id="resolverModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-24 max-w-md w-full mx-16 shadow-lg">
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

    async function marcarResolvidoDireto(id) {
        const response = await fetch(`/cliente/avaliacao/${id}/responder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ resposta: 'Resolvido diretamente pelo painel.' })
        });
        
        if (response.ok) {
            location.reload();
        }
    }

    document.getElementById('resolverForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('modal_avaliacao_id').value;
        const resposta = document.getElementById('modal_anotacao').value;
        
        const response = await fetch(`/cliente/avaliacao/${id}/responder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ resposta })
        });
        
        if (response.ok) {
            location.reload();
        }
    });
</script>
@endsection
