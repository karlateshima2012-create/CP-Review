@extends('layouts.app')

@section('title', 'Todas as Avaliações')

@section('content')
<div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-purple-600">CP Review Care</h1>
            <div class="flex gap-4 items-center">
                <a href="{{ route('cliente.dashboard', $cliente->id) }}" class="text-gray-600 hover:text-purple-600">Dashboard</a>
                <span class="text-gray-400">|</span>
                <span class="text-gray-600">{{ $cliente->nome_empresa }}</span>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold">📋 Todas as Avaliações</h2>
            </div>
            <div class="divide-y">
                @forelse($avaliacoes as $avaliacao)
                <div class="p-6 transition hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-2xl">{{ $avaliacao->stars }}</div>
                            <p class="text-gray-600 mt-2">{{ $avaliacao->feedback ?: 'Sem feedback escrito' }}</p>
                            @if($avaliacao->problema)
                            <span class="inline-block bg-gray-100 text-gray-600 text-sm px-2 py-1 rounded mt-2">{{ $avaliacao->problema }}</span>
                            @endif
                            
                            @if($avaliacao->tipo_contato != 'nao')
                            <div class="mt-2 text-sm text-purple-600">
                                📞 Contato solicitado via {{ ucfirst($avaliacao->tipo_contato) }}: <strong>{{ $avaliacao->contato_valor }}</strong>
                            </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">{{ $avaliacao->created_at->format('d/m/Y H:i') }}</div>
                            @if($avaliacao->resolvido)
                                <span class="inline-block bg-green-100 text-green-600 text-sm px-2 py-1 rounded mt-1">✅ Resolvido</span>
                            @else
                                <span class="inline-block bg-yellow-100 text-yellow-600 text-sm px-2 py-1 rounded mt-1">⏳ Pendente</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">
                    Nenhuma avaliação encontrada.
                </div>
                @endforelse
            </div>
            <div class="p-6 border-t">
                {{ $avaliacoes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
