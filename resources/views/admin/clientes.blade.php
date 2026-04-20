@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">🏢 Clientes</h2>
            <button onclick="alert('Funcionalidade em desenvolvimento: Novos clientes devem ser aprovados via Transações para garantir o fluxo financeiro.')" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-purple-700 transition">
                + Novo Cliente
            </button>
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
                        <td class="px-6 py-4 text-[10px] text-gray-400 font-mono">...{{ substr($cliente->id, -8) }}</td>
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
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.clientes.qrcode', $cliente->id) }}" class="text-blue-600 hover:text-blue-800 font-medium" title="Gerar QR Code">
                                    🖼️ QR
                                </a>
                                <a href="{{ route('admin.clientes.edit', $cliente->id) }}" class="text-purple-600 hover:text-purple-800 font-medium" title="Editar Cliente">
                                    ✏️ Editar
                                </a>
                                <form action="{{ route('admin.clientes.destroy', $cliente->id) }}" method="POST" onsubmit="return confirm('Tem certeza? Isso apagará todos os dados de avaliações deste cliente.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                        🗑️ Excluir
                                    </button>
                                </form>
                            </div>
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
