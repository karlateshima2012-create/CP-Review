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
            'messages' => 'required|array',
            'messages.pt' => 'required|array',
            'messages.ja' => 'required|array',
            'messages.*.*.text' => 'nullable|string|max:1000',
            'messages.*.*.step' => 'nullable|integer|min:1',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'motivos_problema' => 'nullable|array',
            'motivos_problema.*' => 'string|in:atendimento,produto_servico,preco,demora,limpeza,conforto,entrega,outro',
        ]);

        foreach ($validated['messages'] as $locale => $msgs) {
            $botScript = \App\Models\BotScript::firstOrNew([
                'tenant_id' => $cliente->id,
                'locale' => $locale,
            ]);
            $botScript->messages = $msgs;
            $botScript->save();
        }

        // Sincronizar colunas herdadas do cliente para compatibilidade retroativa
        $ptMsgs = $validated['messages']['pt'] ?? [];
        $jaMsgs = $validated['messages']['ja'] ?? [];

        $updateData = [
            'msg_boas_vindas_br' => $ptMsgs['welcome']['text'] ?? $cliente->msg_boas_vindas_br,
            'msg_pergunta_nota_br' => $ptMsgs['askRate']['text'] ?? $cliente->msg_pergunta_nota_br,
            'msg_agradecimento_alta_br' => $ptMsgs['highRate']['text'] ?? $cliente->msg_agradecimento_alta_br,
            'msg_agradecimento_baixa_br' => $ptMsgs['lowRate']['text'] ?? $cliente->msg_agradecimento_baixa_br,
            'msg_boas_vindas_jp' => $jaMsgs['welcome']['text'] ?? $cliente->msg_boas_vindas_jp,
            'msg_pergunta_nota_jp' => $jaMsgs['askRate']['text'] ?? $cliente->msg_pergunta_nota_jp,
            'msg_agradecimento_alta_jp' => $jaMsgs['highRate']['text'] ?? $cliente->msg_agradecimento_alta_jp,
            'msg_agradecimento_baixa_jp' => $jaMsgs['lowRate']['text'] ?? $cliente->msg_agradecimento_baixa_jp,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('clientes', 'motivos_problema')) {
            $updateData['motivos_problema'] = $request->input('motivos_problema', []);
        }

        // Processamento de Upload do Logo
        if ($request->hasFile('logo') && \Illuminate\Support\Facades\Schema::hasColumn('clientes', 'logo_path')) {
            if ($cliente->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($cliente->logo_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cliente->logo_path);
            }
            $updateData['logo_path'] = $request->file('logo')->store('branding/logos', 'public');
        }

        // Processamento de Upload da Capa
        if ($request->hasFile('cover') && \Illuminate\Support\Facades\Schema::hasColumn('clientes', 'cover_path')) {
            if ($cliente->cover_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($cliente->cover_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cliente->cover_path);
            }
            $updateData['cover_path'] = $request->file('cover')->store('branding/covers', 'public');
        }

        $cliente->update($updateData);

        return redirect()->back()->with('success', 'Configurações de personalização salvas com sucesso!');
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

        $botScriptPt = \App\Models\BotScript::where('tenant_id', $cliente->id)
            ->where('locale', 'pt')
            ->first();

        $botScriptJp = \App\Models\BotScript::where('tenant_id', $cliente->id)
            ->where('locale', 'ja')
            ->first();

        $defaultsPt = \App\Models\BotScript::getDefaultMessages('pt');
        $defaultsJp = \App\Models\BotScript::getDefaultMessages('ja');

        $savedPt = $botScriptPt ? $botScriptPt->messages : [];
        $savedJp = $botScriptJp ? $botScriptJp->messages : [];

        $messagesPt = [];
        foreach ($defaultsPt as $key => $defaultVal) {
            $messagesPt[$key] = [
                'text' => $savedPt[$key]['text'] ?? $defaultVal['text'],
                'step' => isset($savedPt[$key]['step']) ? $savedPt[$key]['step'] : $defaultVal['step'],
            ];
        }

        $messagesJp = [];
        foreach ($defaultsJp as $key => $defaultVal) {
            $messagesJp[$key] = [
                'text' => $savedJp[$key]['text'] ?? $defaultVal['text'],
                'step' => isset($savedJp[$key]['step']) ? $savedJp[$key]['step'] : $defaultVal['step'],
            ];
        }

        return view('cliente.bot', compact('cliente', 'messagesPt', 'messagesJp'));
    }
}
