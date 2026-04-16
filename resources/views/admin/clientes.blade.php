@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">🏢 Clientes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plano</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avaliações</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($clientes as $cliente)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">#{{ $cliente->id }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $cliente->nome_empresa }}</div>
                            <div class="text-sm text-gray-500">Slug: {{ $cliente->slug }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($cliente->email)<div>📧 {{ $cliente->email }}</div>@endif
                            @if($cliente->telefone_whatsapp)<div>📱 {{ $cliente->telefone_whatsapp }}</div>@endif
                            @if($cliente->line_user_id)<div>💬 {{ $cliente->line_user_id }}</div>@endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="capitalize px-2 py-1 rounded-full text-xs font-medium
                                {{ $cliente->plano == 'premium' ? 'bg-purple-100 text-purple-700' : ($cliente->plano == 'standard' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $cliente->plano }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $cliente->avaliacoes_count }} avaliações</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">Ativo</span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('cliente.dashboard', $cliente->id) }}" target="_blank" class="text-purple-600 hover:text-purple-800 text-sm">Ver Painel</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $clientes->links() }}
        </div>
    </div>
</div>
@endsection
