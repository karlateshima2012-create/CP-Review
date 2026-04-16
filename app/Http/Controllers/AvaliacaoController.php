<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Avaliacao;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AvaliacaoController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function show($slug)
    {
        $cliente = Cliente::where('slug', $slug)->firstOrFail();
        return view('avaliacao.index', compact('cliente'));
    }

    public function salvar(Request $request, $slug)
    {
        $cliente = Cliente::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'nota' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
            'problema' => 'nullable|string',
            'tipo_contato' => 'nullable|in:whatsapp,line,nao',
            'contato_valor' => 'nullable|string',
            'nome_cliente' => 'nullable|string|max:255',
            'primeira_visita' => 'nullable|boolean',
            'periodo_visita' => 'nullable|string',
            'foto_problema' => 'nullable|string' // base64
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function() use ($validated, $cliente) {
            $token = \Illuminate\Support\Str::random(64);
            $fotoPath = null;

            if (!empty($validated['foto_problema'])) {
                $fotoData = $validated['foto_problema'];
                if (preg_match('/^data:image\/(\w+);base64,/', $fotoData, $type)) {
                    $fotoData = substr($fotoData, strpos($fotoData, ',') + 1);
                    $extension = strtolower($type[1]);
                    $fotoData = base64_decode($fotoData);
                    $fileName = \Illuminate\Support\Str::uuid() . '.' . $extension;
                    \Illuminate\Support\Facades\Storage::disk('public')->put('avaliacoes/' . $fileName, $fotoData);
                    $fotoPath = 'avaliacoes/' . $fileName;
                }
            }

            $avaliacao = Avaliacao::create([
                'cliente_id' => $cliente->id,
                'nota' => $validated['nota'],
                'feedback' => $validated['feedback'] ?? null,
                'problema' => $validated['problema'] ?? null,
                'nome_cliente' => $validated['nome_cliente'] ?? 'Anônimo',
                'tipo_contato' => $validated['tipo_contato'] ?? 'nao',
                'contato_valor' => $validated['contato_valor'] ?? null,
                'token_resposta' => $token,
                'primeira_visita' => $validated['primeira_visita'] ?? false,
                'periodo_visita' => $validated['periodo_visita'] ?? null,
                'foto_problema' => $fotoPath
            ]);

            // Se for nota baixa e cliente usa WhatsApp, enviar notificação
            if ($avaliacao->isNegativa() && 
                $cliente->canal_notificacao === 'whatsapp' && 
                $cliente->telefone_whatsapp) {
                
                $this->whatsappService->sendNegativeNotification($cliente, $avaliacao);
            }

            return response()->json([
                'success' => true,
                'message' => 'Avaliação salva com sucesso',
                'token' => $token
            ]);
        });
    }
}
