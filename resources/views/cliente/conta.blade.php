@extends('layouts.app')

@section('title', 'Minha Conta')

@section('content')
<div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-purple-600">CP Review Care</h1>
            <div class="flex gap-4 items-center">
                <a href="{{ route('cliente.dashboard', $cliente->id) }}" class="text-gray-600 hover:text-purple-600">Dashboard</a>
                <span class="text-gray-400">|</span>
                <a href="{{ route('cliente.avaliacoes', $cliente->id) }}" class="text-gray-600 hover:text-purple-600">Avaliações</a>
                <span class="text-gray-400">|</span>
                <span class="text-gray-900 font-bold">Minha Conta</span>
                <span class="text-gray-400">|</span>
                <span class="text-gray-600">{{ $cliente->nome_empresa }}</span>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">⚙️ Configurações da Conta</h2>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <p class="font-bold">Sucesso!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <p class="font-bold">Ocorreu um erro:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid md:grid-cols-3 gap-6">
            <!-- Informações Gerais do Estabelecimento -->
            <div class="md:col-span-2 bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-purple-600 mb-4 pb-2 border-b">🏢 Detalhes do Estabelecimento</h3>
                
                <form action="{{ route('cliente.conta.update', $cliente->id) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome da Empresa</label>
                            <input type="text" name="nome_empresa" value="{{ old('nome_empresa', $cliente->nome_empresa) }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-mail de Contato / Login</label>
                            <input type="email" name="email" value="{{ old('email', $cliente->email) }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telefone WhatsApp (alertas)</label>
                                <input type="text" name="telefone_whatsapp" value="{{ old('telefone_whatsapp', $cliente->telefone_whatsapp) }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1 focus:outline-none focus:ring-2 focus:ring-purple-600" placeholder="Ex: 5511999999999">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">LINE User ID (Japão)</label>
                                <input type="text" name="line_user_id" value="{{ old('line_user_id', $cliente->line_user_id) }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1 focus:outline-none focus:ring-2 focus:ring-purple-600">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Link de Avaliações do Google Maps</label>
                            <input type="url" name="google_maps_link" value="{{ old('google_maps_link', $cliente->google_maps_link) }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1 focus:outline-none focus:ring-2 focus:ring-purple-600" placeholder="https://g.page/r/...">
                            <p class="text-xs text-gray-400 mt-1">Os clientes que derem notas 4 ou 5 serão sugeridos a acessar este link.</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold text-purple-600 mt-8 mb-4 pb-2 border-b">🔒 Alterar Senha (Opcional)</h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nova Senha</label>
                                <input type="password" name="password" class="w-full border rounded-lg px-3 py-2 text-sm mt-1 focus:outline-none focus:ring-2 focus:ring-purple-600">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                                <input type="password" name="password_confirmation" class="w-full border rounded-lg px-3 py-2 text-sm mt-1 focus:outline-none focus:ring-2 focus:ring-purple-600">
                            </div>
                        </div>
                        <p class="text-xs text-gray-400">Deixe os campos de senha em branco se não quiser alterá-la.</p>
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-bold hover:bg-purple-700 transition shadow">
                            Gravar Configurações
                        </button>
                    </div>
                </form>
            </div>

            <!-- Resumo e Informações de Ajuda -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-md font-bold mb-3">📍 Plano Atual</h3>
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full uppercase">{{ $cliente->plano }}</span>
                        <span class="text-sm text-gray-500">
                            @if($cliente->trial_ends_at)
                                Trial até {{ $cliente->trial_ends_at->format('d/m/Y') }}
                            @else
                                Ativo
                            @endif
                        </span>
                    </div>
                    <div class="border-t pt-4 text-xs text-gray-500 space-y-2">
                        <p><strong>País:</strong> {{ strtoupper($cliente->pais) }}</p>
                        <p><strong>Notificação Ativa por:</strong> {{ ucfirst($cliente->canal_notificacao) }}</p>
                        <p><strong>Data de Ativação:</strong> {{ $cliente->data_ativacao ? $cliente->data_ativacao->format('d/m/Y') : 'Não ativa' }}</p>
                    </div>
                </div>

                <div class="bg-purple-50 rounded-xl p-6 border border-purple-100">
                    <h3 class="text-md font-bold text-purple-900 mb-2">💡 Dica</h3>
                    <p class="text-sm text-purple-700 leading-relaxed">
                        Mantenha seu <strong>Link do Google Maps</strong> atualizado. Ele é o principal canal para impulsionar a pontuação geral da sua empresa no Google Local.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
