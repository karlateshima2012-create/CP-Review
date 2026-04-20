@extends('layouts.admin')

@section('title', 'Monitor de Notificações')

@section('header', 'Monitor de Notificações')

@section('content')
<div class="p-8 space-y-8">
    <!-- Stats Banner -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Enviadas</p>
            <h3 class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</h3>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm border-l-4 border-l-green-500">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sucesso</p>
            <h3 class="text-3xl font-black text-green-600">{{ $stats['sucesso'] }}</h3>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm border-l-4 border-l-red-500">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Falhas</p>
            <h3 class="text-3xl font-black text-red-600">{{ $stats['falha'] }}</h3>
        </div>
        <div class="bg-gray-900 p-6 rounded-3xl shadow-xl">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Taxa de Entrega</p>
            <h3 class="text-3xl font-black text-white">{{ $stats['taxa'] }}%</h3>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <form class="flex flex-wrap gap-4 items-end">
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Canal</label>
                <select name="canal" class="block w-40 rounded-xl border-gray-100 text-sm font-bold bg-gray-50 focus:ring-purple-500">
                    <option value="">Todos</option>
                    <option value="whatsapp" {{ request('canal') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="line" {{ request('canal') == 'line' ? 'selected' : '' }}>LINE</option>
                    <option value="email" {{ request('canal') == 'email' ? 'selected' : '' }}>E-mail</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</label>
                <select name="status" class="block w-40 rounded-xl border-gray-100 text-sm font-bold bg-gray-50 focus:ring-purple-500">
                    <option value="">Todos</option>
                    <option value="enviada" {{ request('status') == 'enviada' ? 'selected' : '' }}>Enviada</option>
                    <option value="falha" {{ request('status') == 'falha' ? 'selected' : '' }}>Falha</option>
                </select>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-purple-700 transition">Filtrar</button>
            <a href="{{ route('admin.notifications') }}" class="text-xs font-bold text-gray-400 hover:text-gray-600 mb-3 px-2">Limpar</a>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data/Hora</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tenant</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Canal</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Destinatário</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 text-xs font-bold text-gray-500">
                        {{ $log->created_at->format('d/m/Y H:i') }}
                        @if($log->retries > 0)
                        <span class="ml-1 text-orange-500">({{ $log->retries }} retentativas)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">{{ $log->tenant->nome_empresa }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $log->canal == 'whatsapp' ? 'bg-green-100 text-green-700' : ($log->canal == 'line' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                            {{ $log->canal }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs font-mono text-gray-500">{{ $log->destinatario }}</td>
                    <td class="px-6 py-4">
                        @if($log->status == 'enviada')
                        <span class="flex items-center text-green-600 font-bold text-xs uppercase tracking-tighter">
                            <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-2"></span> Enviada
                        </span>
                        @else
                        <div class="group relative">
                            <span class="flex items-center text-red-600 font-bold text-xs uppercase tracking-tighter cursor-help">
                                <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-2"></span> Falha
                            </span>
                            <div class="absolute bottom-full left-0 mb-2 w-64 p-2 bg-gray-900 text-white text-[10px] rounded-lg opacity-0 group-hover:opacity-100 transition pointer-events-none z-50">
                                {{ $log->erro_mensagem ?: 'Erro desconhecido' }}
                            </div>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($log->status == 'falha')
                        <form action="{{ route('admin.notifications.retry', $log->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-purple-600 hover:text-purple-800 text-xs font-black uppercase tracking-widest">Reenviar</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-bold italic">Nenhum log de notificação encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
