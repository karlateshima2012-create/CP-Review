@extends('layouts.admin')

@section('title', 'Transações')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">💰 Transações</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plano</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transacoes as $transacao)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $transacao->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $transacao->empresa }}</div>
                            <div class="text-sm text-gray-500">{{ $transacao->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">R$ {{ number_format($transacao->valor, 2) }}</td>
                        <td class="px-6 py-4 text-sm capitalize">{{ $transacao->plano }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                {{ $transacao->status == 'aprovado' ? 'bg-green-100 text-green-700' : ($transacao->status == 'rejeitado' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($transacao->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transacoes->links() }}
        </div>
    </div>
</div>
@endsection
