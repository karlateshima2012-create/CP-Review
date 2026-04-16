<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Avaliacao;
use App\Services\WhatsAppService;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
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

    public function avaliacoes(Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        $avaliacoes = $cliente->avaliacoes()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('cliente.avaliacoes', compact('cliente', 'avaliacoes'));
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

        // Se a avaliação foi via WhatsApp, envia a resposta de encerramento
        if ($avaliacao->tipo_contato === 'whatsapp' && $avaliacao->contato_valor) {
            $this->whatsappService->sendResponseToCustomer($avaliacao);
        }

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
}
