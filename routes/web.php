<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AvaliacaoController;
use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/temp-reset-db-9f2bc', function() {
    try {
        // 1. Reset/Create Admin
        $admin = \App\Models\User::where('email', 'admin@cpreview.com')->first();
        if ($admin) {
            $admin->password = \Illuminate\Support\Facades\Hash::make('admin123');
            $admin->save();
        } else {
            $admin = \App\Models\User::create([
                'name' => 'Admin CP Review',
                'email' => 'admin@cpreview.com',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'role' => 'owner'
            ]);
        }

        // 2. Reset/Create Lojista
        $loja = \App\Models\User::where('email', 'loja@teste.com')->first();
        if ($loja) {
            $loja->password = \Illuminate\Support\Facades\Hash::make('loja123');
            $loja->save();
        } else {
            $loja = \App\Models\User::create([
                'name' => 'Lojista de Teste',
                'email' => 'loja@teste.com',
                'password' => \Illuminate\Support\Facades\Hash::make('loja123'),
                'role' => 'owner'
            ]);
        }

        // 3. Link Cliente to Lojista
        $c = \App\Models\Cliente::where('user_id', $loja->id)->first();
        if (!$c) {
            $c = \App\Models\Cliente::where('slug', 'creative-print')->first();
        }
        if (!$c) {
            $c = \App\Models\Cliente::where('slug', 'sabor-senior')->first();
        }
        
        if ($c) {
            $c->user_id = $loja->id;
            $c->save();
            $loja->tenant_id = $c->id;
            $loja->save();
        } else {
            $c = \App\Models\Cliente::create([
                'user_id' => $loja->id,
                'nome_empresa' => 'CREATIVE PRINT',
                'email' => 'contato@creativeprint.com',
                'slug' => 'creative-print',
                'google_maps_link' => 'https://g.page/r/CT0IMW6LPFnnEBM/review',
                'telefone_whatsapp' => '5511999999999',
                'plano' => 'elite',
                'ativo' => true,
                'data_ativacao' => now(),
            ]);
            $loja->tenant_id = $c->id;
            $loja->save();
        }

        // 4. Reset/Create Tanaka
        $tanaka = \App\Models\User::where('email', 'tanaka@test.jp')->first();
        if ($tanaka) {
            $tanaka->password = \Illuminate\Support\Facades\Hash::make('tanaka123');
            $tanaka->save();
        } else {
            $tanaka = \App\Models\User::create([
                'name' => 'Tanaka-san',
                'email' => 'tanaka@test.jp',
                'password' => \Illuminate\Support\Facades\Hash::make('tanaka123'),
                'role' => 'owner'
            ]);
        }

        $cT = \App\Models\Cliente::where('user_id', $tanaka->id)->first();
        if (!$cT) {
            $cT = \App\Models\Cliente::where('slug', 'tanaka-sushi')->first();
        }
        if ($cT) {
            $cT->user_id = $tanaka->id;
            $cT->save();
            $tanaka->tenant_id = $cT->id;
            $tanaka->save();
        } else {
            $cT = \App\Models\Cliente::create([
                'user_id' => $tanaka->id,
                'nome_empresa' => '田中寿司 (Tanaka Sushi)',
                'slug' => 'tanaka-sushi',
                'pais' => 'jp',
                'canal_notificacao' => 'line',
                'line_user_id' => 'U1234567890abcdef',
                'plano' => 'elite',
                'ativo' => true,
            ]);
            $tanaka->tenant_id = $cT->id;
            $tanaka->save();
        }

        // 5. Clear session support mode
        session()->forget('impersonate_tenant_id');

        return "Sucesso! Credenciais criadas/atualizadas no banco e sessao de simulacao limpa.";
    } catch (\Exception $e) {
        return "Erro: " . $e->getMessage() . "\n" . $e->getTraceAsString();
    }
});

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/contratar', [LandingController::class, 'contratar']);

// Link Curto Redirecionamento
Route::get('/r/{slug}', function($slug) {
    return redirect()->route('avaliacao.show', $slug);
});

// Avaliação (pública)
Route::get('/avaliar/{slug}', [AvaliacaoController::class, 'show'])->name('avaliacao.show');
Route::post('/avaliar/{slug}/salvar', [AvaliacaoController::class, 'salvar'])
    ->name('avaliacao.salvar')
    ->middleware('throttle:avaliacoes');
Route::get('/api/bot-script/{slug}', [AvaliacaoController::class, 'botScript'])->name('api.botScript');
Route::post('/api/media/upload', [AvaliacaoController::class, 'uploadMedia'])
    ->name('api.mediaUpload')
    ->middleware('throttle:avaliacoes');
// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Painel do Cliente (Protegido por login)
Route::middleware(['auth'])->prefix('/cliente')->group(function () {
    Route::get('/{cliente}', [ClienteController::class, 'dashboard'])->name('cliente.dashboard');
    Route::post('/{cliente}/perfil', [ClienteController::class, 'updatePerfil'])->name('cliente.perfil.update');
    Route::get('/{cliente}/avaliacoes', [ClienteController::class, 'avaliacoes'])->name('cliente.avaliacoes');
    Route::get('/{cliente}/conta', [ClienteController::class, 'showConta'])->name('cliente.conta');
    Route::post('/{cliente}/conta', [ClienteController::class, 'updateConta'])->name('cliente.conta.update');
    Route::post('/avaliacao/{avaliacao}/responder', [ClienteController::class, 'responder']);
    Route::get('/qrcode/{cliente}', [ClienteController::class, 'qrcode']);
    Route::get('/qrcode/{cliente}/download', [ClienteController::class, 'downloadQrCode']);
});

// Painel Admin (Proteger com middleware robusto)
Route::prefix('/admin')->middleware(['auth', 'admin.auth'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/clientes', [AdminController::class, 'clientes'])->name('admin.clientes');
    Route::get('/clientes/create', [AdminController::class, 'createCliente'])->name('admin.clientes.create');
    Route::post('/clientes/store', [AdminController::class, 'storeCliente'])->name('admin.clientes.store');
    Route::get('/clientes/export', [AdminController::class, 'exportClientes'])->name('admin.clientes.export');
    Route::get('/clientes/{cliente}/impersonate', [AdminController::class, 'impersonate'])->name('admin.clientes.impersonate');
    Route::get('/stop-impersonation', [AdminController::class, 'stopImpersonation'])->name('admin.stop-impersonation');
    
    Route::get('/clientes/{cliente}/edit', [AdminController::class, 'editCliente'])->name('admin.clientes.edit');
    Route::post('/clientes/{cliente}/update', [AdminController::class, 'updateCliente'])->name('admin.clientes.update');
    Route::delete('/clientes/{cliente}', [AdminController::class, 'destroyCliente'])->name('admin.clientes.destroy');
    Route::get('/clientes/{cliente}/qrcode', [AdminController::class, 'generateQrCode'])->name('admin.clientes.qrcode');
    Route::post('/clientes/{cliente}/qrcode/branding', [AdminController::class, 'updateQrBranding'])->name('admin.clientes.qrcode.branding');
    
    Route::get('/transacoes', [AdminController::class, 'transacoes'])->name('admin.transacoes');
    Route::get('/notificacoes', [AdminController::class, 'notifications'])->name('admin.notifications');
    Route::post('/notificacoes/{id}/retry', [AdminController::class, 'retryNotification'])->name('admin.notifications.retry');
    
    Route::get('/reports', [\App\Http\Controllers\AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/preview/{id}', [\App\Http\Controllers\AdminReportController::class, 'preview'])->name('admin.reports.preview');
    Route::post('/reports/send', [\App\Http\Controllers\AdminReportController::class, 'send'])->name('admin.reports.send');
    Route::get('/reports/track/{id}', [\App\Http\Controllers\AdminReportController::class, 'track'])->name('admin.reports.track');

    Route::post('/aprovar', [AdminController::class, 'aprovarCliente']);
    Route::post('/rejeitar', [AdminController::class, 'rejeitarCliente']);
});
