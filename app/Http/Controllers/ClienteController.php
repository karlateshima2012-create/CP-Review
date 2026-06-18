<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Avaliacao;
use App\Services\NotificationService;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function dashboard(Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        $totalAvaliacoes = $cliente->avaliacoes()->count();
        $mediaNotas = $cliente->avaliacoes()->avg('nota');
        $negativas = $cliente->avaliacoes()->where('nota', '<=', 3)->count();
        $ultimasAvaliacoes = $cliente->avaliacoes()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Insights de BI
        $pctPrimeiraVisita = $totalAvaliacoes > 0 
            ? ($cliente->avaliacoes()->where('primeira_visita', true)->count() / $totalAvaliacoes) * 100 
            : 0;
            
        $periodos = $cliente->avaliacoes()
            ->select('periodo_visita', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotNull('periodo_visita')
            ->groupBy('periodo_visita')
            ->get();

        return view('cliente.dashboard', compact(
            'cliente', 'totalAvaliacoes', 'mediaNotas', 'negativas', 'ultimasAvaliacoes',
            'pctPrimeiraVisita', 'periodos'
        ));
    }

    public function avaliacoes(Request $request, Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        $query = $cliente->avaliacoes()->where('nota', '<=', 3);

        $filter = $request->query('filter', 'todas');
        if ($filter === 'pendentes') {
            $query->where('resolvido', false);
        } elseif ($filter === 'resolvidas') {
            $query->where('resolvido', true);
        } elseif ($filter === 'com_contato') {
            $query->where('tipo_contato', '!=', 'nao')->whereNotNull('contato_valor');
        } elseif ($filter === 'sem_contato') {
            $query->where(function($q) {
                $q->where('tipo_contato', 'nao')->orWhereNull('contato_valor');
            });
        }

        $avaliacoes = $query->orderBy('created_at', 'desc')->paginate(20);

        // Counts for occurrences tabs
        $totalNegativas = $cliente->avaliacoes()->where('nota', '<=', 3)->count();
        $negativasPendentes = $cliente->avaliacoes()->where('nota', '<=', 3)->where('resolvido', false)->count();
        $negativasResolvidas = $cliente->avaliacoes()->where('nota', '<=', 3)->where('resolvido', true)->count();

        return view('cliente.avaliacoes', compact(
            'cliente', 'avaliacoes', 'filter', 'totalNegativas', 'negativasPendentes', 'negativasResolvidas'
        ));
    }

    public function responder(Request $request, Avaliacao $avaliacao)
    {
        $this->authorize('update', $avaliacao->cliente);
        
        $request->validate([
            'resposta' => 'nullable|string|max:1000'
        ]);

        $avaliacao->update([
            'resposta_dono' => $request->resposta,
            'respondida_em' => now(),
            'resolvido' => true
        ]);

        // Fecha o loop com o cliente informando sobre a resolução em segundo plano
        \App\Jobs\SendCloseLoopNotification::dispatch($avaliacao);

        return redirect()->back()->with('success', 'Ciclo encerrado e cliente notificado!');
    }

    public function qrcode(Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        $url = $cliente->url_avaliacao;

        $qrCode = QrCode::create($url)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return response($result->getString())
            ->header('Content-Type', 'image/png');
    }

    public function downloadQrCode(Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        $url = $cliente->url_avaliacao;

        $qrCode = QrCode::create($url)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return response($result->getString())
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', "attachment; filename=\"qrcode-{$cliente->slug}.png\"");
    }
    public function updatePerfil(Request $request, Cliente $cliente)
    {
        $this->authorize('update', $cliente);

        $validated = $request->validate([
            'msg_boas_vindas_br' => 'required|string|max:255',
            'msg_pergunta_nota_br' => 'required|string|max:255',
            'msg_agradecimento_alta_br' => 'required|string|max:255',
            'msg_agradecimento_baixa_br' => 'required|string|max:255',
            'msg_boas_vindas_jp' => 'required|string|max:255',
            'msg_pergunta_nota_jp' => 'required|string|max:255',
            'msg_agradecimento_alta_jp' => 'required|string|max:255',
            'msg_agradecimento_baixa_jp' => 'required|string|max:255',
        ]);

        $cliente->update($validated);

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso!');
    }

    public function showConta(Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        return view('cliente.conta', compact('cliente'));
    }

    public function updateConta(Request $request, Cliente $cliente)
    {
        $this->authorize('update', $cliente);

        $validated = $request->validate([
            'nome_empresa' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $cliente->user_id . ',id',
            'telefone_whatsapp' => 'nullable|string|max:30',
            'line_user_id' => 'nullable|string|max:255',
            'google_maps_link' => 'nullable|url|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Atualizar Cliente
        $cliente->update([
            'nome_empresa' => $validated['nome_empresa'],
            'email' => $validated['email'],
            'telefone_whatsapp' => $validated['telefone_whatsapp'] ?? null,
            'line_user_id' => $validated['line_user_id'] ?? null,
            'google_maps_link' => $validated['google_maps_link'] ?? null,
        ]);

        // Atualizar User
        $userUpdate = [
            'name' => $validated['nome_empresa'] . " Admin",
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userUpdate['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $cliente->user()->update($userUpdate);

        return redirect()->back()->with('success', 'Dados da conta atualizados com sucesso!');
    }

    public function showQrCodeLink(Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        return view('cliente.qrcode', compact('cliente'));
    }

    public function showBotSettings(Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        return view('cliente.bot', compact('cliente'));
    }
}
