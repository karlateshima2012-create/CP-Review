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
            'pack_idioma' => 'required|in:pt_ja,ja_en',
            'plano'       => 'required|in:standard,pro,elite',
            'empresa'     => 'required|string|max:100',
            'email'       => 'required|email',
            'telefone'    => 'nullable|string',
            'line_id'     => 'nullable|string',
            'canal'       => 'required|in:whatsapp,line',
            'google_maps_link' => 'nullable|url',
        ]);

        $slug = Str::slug($validated['empresa']) . '-' . rand(100, 999);
        while (\App\Models\Cliente::where('slug', $slug)->exists()) {
            $slug = Str::slug($validated['empresa']) . '-' . rand(100, 999);
        }

        $precos = ['standard' => 4800, 'pro' => 7800, 'elite' => 12000];
        $valor = $precos[$validated['plano']] ?? 4800;

        $pack = $validated['pack_idioma'];

        $transacao = Transacao::create([
            'transacao_id' => 'CP_' . uniqid(),
            'empresa'      => $validated['empresa'],
            'email'        => $validated['email'],
            'telefone'     => $validated['telefone'] ?? '',
            'line_id'      => $validated['line_id'] ?? null,
            'plano'        => $validated['plano'],
            'valor'        => $valor,
            'slug'         => $slug,
            'pack_idioma'  => $pack,
            'pais'         => $pack === 'ja_en' ? 'jp' : 'br',
            'canal'        => $validated['canal'],
            'status'       => 'pendente',
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
