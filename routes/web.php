<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AvaliacaoController;
use Illuminate\Support\Facades\Route;

// Landing Page
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
Route::get('/api/bot-script/{slug}', [AvaliacaoController::class, 'botScript'])->name('api.botScript')->middleware('throttle:60,1');
Route::post('/api/media/upload', [AvaliacaoController::class, 'uploadMedia'])
    ->name('api.mediaUpload')
    ->middleware('throttle:avaliacoes');
// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Painel do Cliente (Protegido por login)
Route::middleware(['auth'])->prefix('/cliente')->group(function () {
    Route::get('/{cliente}', [ClienteController::class, 'dashboard'])->name('cliente.dashboard');
    Route::post('/{cliente}/perfil', [ClienteController::class, 'updatePerfil'])->name('cliente.perfil.update');
    Route::get('/{cliente}/avaliacoes', [ClienteController::class, 'avaliacoes'])->name('cliente.avaliacoes');
    Route::get('/{cliente}/conta', [ClienteController::class, 'showConta'])->name('cliente.conta');
    Route::get('/{cliente}/qrcode-link', [ClienteController::class, 'showQrCodeLink'])->name('cliente.qrcode-link');
    Route::get('/{cliente}/bot', [ClienteController::class, 'showBotSettings'])->name('cliente.bot');
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
    Route::post('/clientes/{cliente}/impersonate', [AdminController::class, 'impersonate'])->name('admin.clientes.impersonate');
    Route::post('/stop-impersonation', [AdminController::class, 'stopImpersonation'])->name('admin.stop-impersonation');
    
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
