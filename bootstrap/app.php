<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminMiddleware::class,
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
        ]);
        
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\TenantMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e) {
            // Ignorar erros 404 (página não encontrada)
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 404) {
                return;
            }
            // Ignorar erros de validação
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return;
            }
            // Ignorar erros de autenticação (não logado)
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return;
            }
            // Ignorar erro de CSRF expirado (Token Mismatch)
            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                return;
            }

            try {
                $botToken = env('TELEGRAM_BOT_TOKEN');
                $chatId = env('TELEGRAM_CHAT_ID');

                if ($botToken && $chatId) {
                    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
                    
                    // Diagnóstico de impacto estimado do erro
                    $impacto = "⚠️ <b>Erro de Execução:</b> O usuário provavelmente visualizou uma tela de erro (500) ou uma ação foi interrompida.";
                    
                    if ($e instanceof \Illuminate\Database\QueryException || str_contains(strtolower($e->getMessage()), 'database') || str_contains(strtolower($e->getMessage()), 'connection refused')) {
                        $impacto = "🔴 <b>Banco de Dados Indisponível/Falha!</b> O sistema não consegue se conectar ao MySQL. O usuário está vendo uma tela de erro e o sistema está fora do ar.";
                    } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                        $statusCode = $e->getStatusCode();
                        if ($statusCode === 503) {
                            $impacto = "🚧 <b>Modo de Manutenção Ativo!</b> O usuário está vendo a tela padrão de manutenção (503).";
                        } else {
                            $impacto = "⚠️ <b>Erro HTTP {$statusCode}!</b> O usuário recebeu uma resposta de erro com o status {$statusCode}.";
                        }
                    } elseif ($e instanceof \Error || $e instanceof \ErrorException) {
                        $impacto = "🔴 <b>Erro Fatal no Código (Crash)!</b> Ocorreu uma falha grave (variável indefinida, método inexistente ou syntax error). O usuário viu uma tela de erro (500).";
                    }

                    $message = "⚠️ <b>[Erro de Aplicação]</b> no servidor <code>" . gethostname() . "</code>\n\n";
                    $message .= "💥 <b>Impacto Estimado:</b>\n" . $impacto . "\n\n";
                    $message .= "<b>Mensagem:</b> <code>" . htmlspecialchars($e->getMessage()) . "</code>\n";
                    $message .= "<b>Arquivo:</b> <code>" . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</code>\n";
                    $message .= "<b>URL:</b> <code>" . request()->fullUrl() . "</code>\n";
                    $message .= "<b>IP Cliente:</b> <code>" . request()->ip() . "</code>\n";

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                        'chat_id' => $chatId,
                        'text' => $message,
                        'parse_mode' => 'HTML'
                    ]));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                    curl_exec($ch);
                    curl_close($ch);
                }
            } catch (\Throwable $th) {
                // Silencia qualquer exceção interna do envio para evitar falha catastrófica
            }
        });
    })->create();
