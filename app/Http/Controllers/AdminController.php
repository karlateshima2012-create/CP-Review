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
        $totalClientes = Cliente::count();
        $totalAvaliacoes = Avaliacao::count();
        $mediaNotas = Avaliacao::avg('nota');
        $negativasPendentes = Avaliacao::where('nota', '<=', 3)
            ->where('resolvido', false)
            ->count();
        $transacoesPendentes = Transacao::where('status', 'pendente')
            ->orderBy('created_at', 'desc')
            ->get();
        $ultimasAvaliacoes = Avaliacao::with('cliente')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.dashboard', compact(
            'totalClientes', 'totalAvaliacoes', 'mediaNotas',
            'negativasPendentes', 'transacoesPendentes', 'ultimasAvaliacoes'
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
            'cliente_id' => $cliente->id
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

    public function clientes()
    {
        $clientes = Cliente::withCount('avaliacoes')
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        
        return view('admin.clientes', compact('clientes'));
    }

    public function transacoes()
    {
        $transacoes = Transacao::orderBy('created_at', 'desc')
            ->paginate(30);
        
        return view('admin.transacoes', compact('transacoes'));
    }
}
