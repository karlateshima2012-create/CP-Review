<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Transacao;
use App\Models\Avaliacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        // KPIs Básicos
        $totalClientes = Cliente::count();
        $totalAvaliacoes = Avaliacao::count();
        $mediaNotas = Avaliacao::avg('nota') ?: 0;
        $negativasPendentes = Avaliacao::where('nota', '<=', 3)
            ->where('resolvido', false)
            ->count();

        // 1. Status Breakdown
        $clientesStatus = [
            'ativos' => Cliente::where('status', 'ativo')->count(),
            'trial' => Cliente::where('status', 'trial')->count(),
            'inativos' => Cliente::where('status', 'inativo')->count(),
        ];

        // 2. MRR Consolidada (Baseado nos clientes ativos)
        $mrr = Cliente::where('ativo', true)->sum('valor_mensal');

        // 3. Avaliações Hoje / Mês
        $avaliacoesHoje = Avaliacao::whereDate('created_at', now()->toDateString())->count();
        $avaliacoesMes = Avaliacao::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count();

        // 4. Tenants com Trial Expirando em 7 dias
        $trialsExpirando = Cliente::where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
            ->get();

        // 5. Gráfico de Novos Tenants (últimas 4 semanas)
        $chartTenants = Cliente::selectRaw('WEEK(created_at) as week, COUNT(*) as total')
            ->where('created_at', '>=', now()->subWeeks(4))
            ->groupBy('week')
            ->get();

        // 6. Alertas de Falha (Mock por enquanto ou buscar em logs se existir)
        $alertasFalha = []; 

        $transacoesPendentes = Transacao::where('status', 'pendente')
            ->orderBy('created_at', 'desc')
            ->get();

        $ultimasAvaliacoes = Avaliacao::with('tenant')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.dashboard', compact(
            'totalClientes', 'totalAvaliacoes', 'mediaNotas',
            'negativasPendentes', 'transacoesPendentes', 'ultimasAvaliacoes',
            'clientesStatus', 'mrr', 'avaliacoesHoje', 'avaliacoesMes',
            'trialsExpirando', 'chartTenants', 'alertasFalha'
        ));
    }

    public function aprovarCliente(Request $request)
    {
        $transacao = Transacao::where('transacao_id', $request->transacao_id)
            ->where('status', 'pendente')
            ->first();

        if (!$transacao) {
            return response()->json(['success' => false, 'error' => 'Transação não encontrada']);
        }

        // Criar cliente
        $cliente = Cliente::create([
            'nome_empresa' => $transacao->empresa,
            'email' => $transacao->email,
            'telefone_whatsapp' => $transacao->telefone,
            'line_user_id' => $transacao->line_id,
            'slug' => $transacao->slug,
            'pais' => $transacao->pais,
            'canal_notificacao' => $transacao->canal,
            'plano' => $transacao->plano,
            'ativo' => true,
            'data_ativacao' => now()
        ]);

        $transacao->update([
            'status' => 'aprovado',
            'tenant_id' => $cliente->id
        ]);

        // Enviar e-mail de boas-vindas
        $this->sendWelcomeEmail($cliente);

        return response()->json(['success' => true]);
    }

    public function rejeitarCliente(Request $request)
    {
        $transacao = Transacao::where('transacao_id', $request->transacao_id)
            ->where('status', 'pendente')
            ->first();

        if ($transacao) {
            $transacao->update(['status' => 'rejeitado']);
        }

        return response()->json(['success' => true]);
    }

    private function sendWelcomeEmail($cliente)
    {
        $linkPainel = url("/cliente/{$cliente->id}");
        $linkAvaliacao = $cliente->url_avaliacao;
        $linkQR = $cliente->qr_code_url;

        Mail::send('emails.welcome', compact('cliente', 'linkPainel', 'linkAvaliacao', 'linkQR'), function ($message) use ($cliente) {
            $message->to($cliente->email)
                ->subject('🎉 Seu sistema CP Review Care está ativo!');
        });
    }

    public function clientes(Request $request)
    {
        $query = Cliente::withCount('avaliacoes');

        // Busca
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nome_empresa', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('slug', 'like', "%{$request->search}%");
            });
        }

        // Filtros
        if ($request->plano) $query->where('plano', $request->plano);
        if ($request->pais) $query->where('pais', $request->pais);
        if ($request->status) $query->where('status', $request->status);

        $clientes = $query->orderBy('created_at', 'desc')->paginate(30);
        
        return view('admin.clientes', compact('clientes'));
    }

    public function exportClientes()
    {
        $clientes = Cliente::all();
        $csvHeader = ['ID', 'Empresa', 'Email', 'Plano', 'Status', 'Data Ativação'];
        
        $callback = function() use ($clientes, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            foreach ($clientes as $c) {
                fputcsv($file, [$c->id, $c->nome_empresa, $c->email, $c->plano, $c->status, $c->created_at]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=tenants_" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }

    public function impersonate(Cliente $cliente)
    {
        session(['impersonate_tenant_id' => $cliente->id]);
        
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'impersonate_start',
            'details' => "Iniciado impersonate do tenant: {$cliente->nome_empresa} (#{$cliente->id})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('cliente.dashboard', $cliente->id);
    }

    public function stopImpersonation()
    {
        session()->forget('impersonate_tenant_id');
        return redirect()->route('admin.dashboard')->with('success', 'Voltamos para o painel Master!');
    }

    public function transacoes()
    {
        $transacoes = Transacao::orderBy('created_at', 'desc')
            ->paginate(30);
        
        return view('admin.transacoes', compact('transacoes'));
    }

    public function editCliente(Cliente $cliente)
    {
        return view('admin.clientes-edit', compact('cliente'));
    }

    public function updateCliente(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nome_empresa' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telefone_whatsapp' => 'nullable|string',
            'slug' => 'required|string|unique:clientes,slug,' . $cliente->id . ',id',
            'plano' => 'required|string',
            'google_maps_link' => 'nullable|url',
            'ativo' => 'boolean'
        ]);

        $cliente->update($data);

        return redirect()->route('admin.clientes')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroyCliente(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('admin.clientes')->with('success', 'Cliente removido!');
    }

    public function generateQrCode(Cliente $cliente)
    {
        return view('admin.clientes-qrcode', compact('cliente'));
    }
}
