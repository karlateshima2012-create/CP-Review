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

        // ── Métricas gerais ────────────────────────────────────────────────
        $totalAvaliacoes = $cliente->avaliacoes()->count();
        $mediaNotas      = $cliente->avaliacoes()->avg('nota') ?? 0;
        $positivas       = $cliente->avaliacoes()->where('nota', '>=', 4)->count();
        $negativas       = $cliente->avaliacoes()->where('nota', '<=', 3)->count();
        $totalScans      = \DB::table('bot_acessos')->where('tenant_id', $cliente->id)->count();
        $semAvaliacao    = max(0, $totalScans - $totalAvaliacoes);
        $taxaConversao   = $totalScans > 0 ? round(($totalAvaliacoes / $totalScans) * 100, 1) : 0;

        // ── Histórico recente (todas as avaliações, mais recentes primeiro) ──
        $historicoRecente = $cliente->avaliacoes()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // ── Ocorrências negativas pendentes (para o alerta) ───────────────
        $ocorrenciasPendentes = $cliente->avaliacoes()
            ->where('nota', '<=', 3)
            ->where('resolvido', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // ── Distribuição por estrela ───────────────────────────────────────
        $starCounts = [];
        for ($s = 1; $s <= 5; $s++) {
            $starCounts[$s] = $cliente->avaliacoes()->where('nota', $s)->count();
        }

        return view('cliente.dashboard', compact(
            'cliente', 'totalAvaliacoes', 'mediaNotas',
            'positivas', 'negativas', 'totalScans', 'semAvaliacao', 'taxaConversao',
            'historicoRecente', 'ocorrenciasPendentes',
            'starCounts'
        ));
    }

    public function avaliacoes(Request $request, Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        $query = $cliente->avaliacoes()->where('nota', '<=', 3);

        $filter = $request->query('filter', 'pendentes');
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
        $this->authorize('update', $avaliacao->tenant);

        $request->validate([
            'resposta' => 'nullable|string|max:1000',
            'reabrir'  => 'nullable|boolean',
        ]);

        if ($request->boolean('reabrir')) {
            $avaliacao->update([
                'resolvido'     => false,
                'resposta_dono' => null,
                'respondida_em' => null,
            ]);
            return response()->json(['success' => true, 'acao' => 'reaberta']);
        }

        $avaliacao->update([
            'resposta_dono' => $request->input('resposta'),
            'respondida_em' => now(),
            'resolvido'     => true,
        ]);

        \App\Jobs\SendCloseLoopNotification::dispatch($avaliacao);

        return response()->json(['success' => true, 'acao' => 'resolvida']);
    }

    public function qrcode(Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        $url = $cliente->url_avaliacao;

        $qrCode = new QrCode(
            data: $url,
            size: 300,
            margin: 10
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return response($result->getString())
            ->header('Content-Type', 'image/png');
    }

    public function downloadQrCode(Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        $url = $cliente->url_avaliacao;

        $qrCode = new QrCode(
            data: $url,
            size: 300,
            margin: 10
        );

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
            'messages.*.*.text' => 'nullable|string|max:1000',
            'messages.*.*.step' => 'nullable|integer|min:1',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'motivos_problema' => 'nullable|array',
            'motivos_problema.*' => 'string|in:atendimento,produto_servico,preco,demora,limpeza,conforto,entrega,outro',
            'cor_principal' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
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

        if (\Illuminate\Support\Facades\Schema::hasColumn('clientes', 'cor_principal')) {
            $updateData['cor_principal'] = $validated['cor_principal'] ?? '#7C3AED';
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
            'current_password' => 'required_with:password|nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $user = $cliente->user;
            if (!\Illuminate\Support\Facades\Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'A senha atual está incorreta.'])->withInput();
            }
        }

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

        $pack = $cliente->pack_idioma ?? 'pt_ja';
        $localesMeta = [
            'pt' => ['flag' => '🇧🇷', 'label' => 'Português'],
            'ja' => ['flag' => '🇯🇵', 'label' => '日本語'],
            'en' => ['flag' => '🇺🇸', 'label' => 'English'],
        ];
        $activeLocales = $pack === 'ja_en' ? ['ja', 'en'] : ['pt', 'ja'];

        $localeData = [];
        foreach ($activeLocales as $i => $locale) {
            $saved = \App\Models\BotScript::where('tenant_id', $cliente->id)
                ->where('locale', $locale)
                ->first();

            $defaults = \App\Models\BotScript::getDefaultMessages($locale);
            $savedMessages = $saved ? ($saved->messages ?? []) : [];

            $messages = [];
            foreach ($defaults as $key => $defaultVal) {
                $messages[$key] = [
                    'text' => $savedMessages[$key]['text'] ?? $defaultVal['text'],
                    'step' => $savedMessages[$key]['step'] ?? $defaultVal['step'],
                ];
            }

            $localeData[] = [
                'key'      => 'locale' . ($i + 1),
                'locale'   => $locale,
                'flag'     => $localesMeta[$locale]['flag'],
                'label'    => $localesMeta[$locale]['label'],
                'messages' => $messages,
            ];
        }

        return view('cliente.bot', compact('cliente', 'pack', 'localeData'));
    }
}
