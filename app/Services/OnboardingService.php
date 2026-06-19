<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\User;
use App\Models\BotScript;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OnboardingService
{
    /**
     * Executes the full onboarding flow for a new tenant.
     */
    public function onboard(array $data): Cliente
    {
        return \DB::transaction(function() use ($data) {
            // 01 & 02: Create Tenant
            $pack = $data['pack_idioma'] ?? 'pt_ja';

            $cliente = Cliente::create([
                'nome_empresa' => $data['nome_empresa'],
                'email' => $data['email'],
                'slug' => Str::slug($data['slug'] ?? $data['nome_empresa']),
                'telefone_whatsapp' => $data['telefone_whatsapp'] ?? null,
                'line_user_id' => $data['line_user_id'] ?? null,
                'pack_idioma' => $pack,
                'pais' => $pack === 'ja_en' ? 'jp' : 'br',
                'canal_notificacao' => $data['canal_notificacao'] ?? ($pack === 'ja_en' ? 'line' : 'whatsapp'),
                'plano' => $data['plano'] ?? 'standard',
                'google_maps_link' => $data['google_maps_link'] ?? null,
                'status' => 'ativo',
                'ativo' => true,
                'data_ativacao' => now(),
                'valor_mensal' => $data['valor_mensal'] ?? 0,
            ]);

            // 03: Generate Default Bot Script
            $this->generateDefaultBotScript($cliente);

            // 04: Generate Branding (Handled by existing logic or specific path)
            // Note: Real PNG/PDF generation is complex with GD/DomPDF, 
            // for now we set defaults and log. Actual export is done in UI ADM-04.
            Storage::makeDirectory("qrcodes/{$cliente->id}");

            // 05: Create Owner User
            $password = Str::random(12);
            $user = User::create([
                'name' => $cliente->nome_empresa . " Admin",
                'email' => $cliente->email,
                'password' => Hash::make($password),
                'tenant_id' => $cliente->id,
                'role' => 'owner'
            ]);

            // Enviar e-mail de boas-vindas com credenciais
            Mail::send('emails.welcome_credentials', [
                'cliente' => $cliente,
                'user' => $user,
                'password' => $password
            ], function ($message) use ($cliente) {
                $message->to($cliente->email)
                    ->subject("🚀 Bem-vindo ao CP Review: Suas Credenciais de Acesso");
            });

            // 06: Log Audit
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'onboarding_complete',
                'details' => "Onboarding finalizado para: {$cliente->nome_empresa} (#{$cliente->id})",
                'ip_address' => request()->ip()
            ]);

            return $cliente;
        });
    }

    protected function generateDefaultBotScript(Cliente $cliente)
    {
        $pack = $cliente->pack_idioma ?? 'pt_ja';
        $locales = $pack === 'ja_en' ? ['ja', 'en'] : ['pt', 'ja'];

        foreach ($locales as $locale) {
            BotScript::create([
                'tenant_id' => $cliente->id,
                'locale' => $locale,
                'messages' => BotScript::getDefaultMessages($locale),
            ]);
        }
    }
}
