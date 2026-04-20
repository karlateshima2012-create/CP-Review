<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Transacao;
use App\Models\Avaliacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    public function dashboard()
    {
        $hasStatus = Schema::hasColumn('clientes', 'status');
        $hasValue = Schema::hasColumn('clientes', 'valor_mensal');
        $hasTrial = Schema::hasColumn('clientes', 'trial_ends_at');

        // KPIs Básicos
        $totalClientes = Cliente::count();
        $totalAvaliacoes = Avaliacao::count();
        $mediaNotas = Avaliacao::avg('nota') ?: 0;
        $negativasPendentes = Avaliacao::where('nota', '<=', 3)
            ->where('resolvido', false)
            ->count();

        // 1. Status Breakdown (Com Fallback defensivo)
        $clientesStatus = [
            'ativos' => $hasStatus ? Cliente::where('status', 'ativo')->count() : Cliente::where('ativo', true)->count(),
            'trial' => $hasStatus ? Cliente::where('status', 'trial')->count() : 0,
            'inativos' => $hasStatus ? Cliente::where('status', 'inativo')->count() : Cliente::where('ativo', false)->count(),
        ];

        // 2. MRR Consolidada (Baseado nos clientes ativos)
        $mrr = ($hasValue && $hasStatus) ? Cliente::where('status', 'ativo')->sum('valor_mensal') : 0;

        // 3. Avaliações Hoje / Mês
        $avaliacoesHoje = Avaliacao::whereDate('created_at', now()->toDateString())->count();
        $avaliacoesMes = Avaliacao::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count();

        // 4. Tenants com Trial Expirando em 7 dias (Defensivo)
        $trialsExpirando = ($hasStatus && $hasTrial) ? Cliente::where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
            ->get() : collect();

        // 5. Gráfico de Novos Tenants (últimas 4 semanas) - Agonóstico a banco de dados
        $chartTenants = Cliente::select('created_at')
            ->where('created_at', '>=', now()->subWeeks(4))
            ->get()
            ->groupBy(function($date) {
                return $date->created_at->format('W'); // Semana do ano
            })
            ->map(function($week, $key) {
                return (object) ['week' => $key, 'total' => $week->count()];
            })
            ->values();

        // 6. Alertas de Falha
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
        $hasStatus = \Illuminate\Support\Facades\Schema::hasColumn('clientes', 'status');
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
        if ($request->status && $hasStatus) $query->where('status', $request->status);

        $clientes = $query->orderBy('created_at', 'desc')->paginate(30);
        
        return view('admin.clientes', compact('clientes', 'hasStatus'));
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
        $historico = \App\Models\AuditLog::where('details', 'like', "%QR%#{$cliente->id}%")
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.clientes-qrcode', compact('cliente', 'historico'));
    }

    public function updateQrBranding(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'qr_color' => 'required|string|size:7',
            'qr_logo' => 'nullable|image|max:1024'
        ]);

        if ($request->hasFile('qr_logo')) {
            $path = $request->file('qr_logo')->store('qr_logos', 'public');
            $cliente->qr_logo_path = $path;
        }

        $cliente->qr_color = $request->qr_color;
        $cliente->save();

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'qr_branding_update',
            'details' => "Atualizado branding do QR Code para o tenant: {$cliente->nome_empresa} (#{$cliente->id})",
            'ip_address' => request()->ip()
        ]);

        return back()->with('success', 'Branding do QR Code atualizado!');
    }
}
