@extends('layouts.admin')

@section('title', 'Editar Cliente')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-xl font-semibold text-gray-800">✏️ Editar: {{ $cliente->nome_empresa }}</h2>
            </div>
            
            <form action="{{ route('admin.clientes.update', $cliente->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Empresa</label>
                        <input type="text" name="nome_empresa" value="{{ $cliente->nome_empresa }}" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL (Slug)</label>
                        <input type="text" name="slug" value="{{ $cliente->slug }}" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        <p class="text-[10px] text-gray-400 mt-1">Ex: cpreview.com/avaliar/<b>nome-da-loja</b></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail para Notificações</label>
                        <input type="email" name="email" value="{{ $cliente->email }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp de Notificação</label>
                        <input type="text" name="telefone_whatsapp" value="{{ $cliente->telefone_whatsapp }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plano</label>
                        <select name="plano" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="standard" {{ $cliente->plano == 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="premium" {{ $cliente->plano == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="elite" {{ $cliente->plano == 'elite' ? 'selected' : '' }}>Elite</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="ativo" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="1" {{ $cliente->ativo ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ !$cliente->ativo ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link Google Maps (Review)</label>
                    <input type="url" name="google_maps_link" value="{{ $cliente->google_maps_link }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" placeholder="https://g.page/r/...">
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-between items-center">
                    <a href="{{ route('admin.clientes') }}" class="text-gray-500 hover:text-gray-700 font-medium">Cancelar</a>
                    <button type="submit" class="bg-purple-600 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
