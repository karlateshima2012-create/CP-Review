<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index');
    }

    public function contratar(Request $request)
    {
        $validated = $request->validate([
            'plano' => 'required|in:lite,standard,premium',
            'empresa' => 'required|string|max:100',
            'email' => 'required|email',
            'telefone' => 'required|string',
            'line_id' => 'nullable|string',
            'canal' => 'required|in:email,whatsapp,line',
            'pais' => 'required|in:br,jp'
        ]);

        // Gerar slug único
        $slug = Str::slug($validated['empresa']) . '-' . rand(100, 999);
        
        // Verificar slug único
        while (\App\Models\Cliente::where('slug', $slug)->exists()) {
            $slug = Str::slug($validated['empresa']) . '-' . rand(100, 999);
        }

        $precos = ['lite' => 49, 'standard' => 97, 'premium' => 197];
        $valor = $precos[$validated['plano']] ?? 97;

        $transacao = Transacao::create([
            'transacao_id' => 'CP_' . uniqid(),
            'empresa' => $validated['empresa'],
            'email' => $validated['email'],
            'telefone' => $validated['telefone'],
            'line_id' => $validated['line_id'] ?? null,
            'plano' => $validated['plano'],
            'valor' => $valor,
            'slug' => $slug,
            'pais' => $validated['pais'],
            'canal' => $validated['canal'],
            'status' => 'pendente'
        ]);

        // Gerar código PIX (simulado - integrar com Asaas/Mercado Pago)
        $pixCode = $this->generatePixCode($transacao);

        return response()->json([
            'success' => true,
            'transacao_id' => $transacao->transacao_id,
            'pix_code' => $pixCode
        ]);
    }

    private function generatePixCode($transacao)
    {
        // Simulação - substituir por integração real
        return "00020126360014BR.GOV.BCB.PIX0114{$transacao->telefone}" .
               "5204000053039865405{$transacao->valor}5802BR5925{$transacao->empresa}" .
               "6009SAOPAULO62070503***6304E2D9";
    }
}
