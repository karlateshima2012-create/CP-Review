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

// Avaliação (pública)
Route::get('/avaliar/{slug}', [AvaliacaoController::class, 'show'])->name('avaliacao.show');
Route::post('/avaliar/{slug}/salvar', [AvaliacaoController::class, 'salvar'])
    ->name('avaliacao.salvar')
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
    Route::post('/avaliacao/{avaliacao}/responder', [ClienteController::class, 'responder']);
    Route::get('/qrcode/{cliente}', [ClienteController::class, 'qrcode']);
    Route::get('/qrcode/{cliente}/download', [ClienteController::class, 'downloadQrCode']);
});

// Painel Admin (Proteger com middleware robusto)
Route::prefix('/admin')->middleware(['auth', 'admin.auth'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/clientes', [AdminController::class, 'clientes'])->name('admin.clientes');
    Route::get('/transacoes', [AdminController::class, 'transacoes'])->name('admin.transacoes');
    Route::post('/aprovar', [AdminController::class, 'aprovarCliente']);
    Route::post('/rejeitar', [AdminController::class, 'rejeitarCliente']);
});
