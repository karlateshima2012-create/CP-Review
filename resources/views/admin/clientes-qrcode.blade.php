@extends('layouts.admin')

@section('title', 'Gerador de QR Code')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden text-center">
            <div class="p-8 bg-purple-600 text-white">
                <h2 class="text-2xl font-bold">{{ $cliente->nome_empresa }}</h2>
                <p class="opacity-80 text-sm mt-1">Gerador de Acesso CP Review Care</p>
            </div>
            
            <div class="p-10 flex flex-col items-center">
                <div class="p-4 bg-white rounded-3xl shadow-2xl border-4 border-purple-50 mb-8">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=400x400&data={{ urlencode($cliente->url_avaliacao) }}" alt="QR Code" class="w-64 h-64">
                </div>

                <div class="space-y-4 w-full">
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-1">URL de Avaliação</p>
                        <p class="text-sm font-medium text-gray-800 break-all">{{ $cliente->url_avaliacao }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <a href="https://api.qrserver.com/v1/create-qr-code/?size=1000x1000&data={{ urlencode($cliente->url_avaliacao) }}" target="_blank" download class="flex items-center justify-center gap-2 bg-purple-600 text-white py-3 rounded-xl font-bold hover:bg-purple-700 transition">
                            📥 Baixar QR
                        </a>
                        <button onclick="window.print()" class="flex items-center justify-center gap-2 bg-gray-800 text-white py-3 rounded-xl font-bold hover:bg-gray-900 transition">
                            🖨️ Imprimir
                        </button>
                    </div>
                    
                    <a href="{{ route('admin.clientes') }}" class="inline-block pt-4 text-gray-400 hover:text-purple-600 text-sm font-medium">
                        ← Voltar para a lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
