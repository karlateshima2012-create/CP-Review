<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Avaliacao;
use App\Models\ReportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class AdminReportController extends Controller
{
    public function index()
    {
        $hasTable = Schema::hasTable('report_logs');
        $logs = $hasTable ? ReportLog::with('tenant')->orderBy('created_at', 'desc')->paginate(30) : collect();
        $tenants = Cliente::all();
        
        return view('admin.reports.index', compact('logs', 'tenants', 'hasTable'));
    }

    public function preview($cliente_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);
        $stats = $this->calculateStats($cliente);
        
        return view('emails.monthly-report', compact('cliente', 'stats'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required', // 'all' or specific ID
        ]);

        $tenants = [];
        if ($request->tenant_id === 'all') {
            $tenants = Cliente::where('ativo', true)->get();
        } else {
            $tenants = [Cliente::findOrFail($request->tenant_id)];
        }

        foreach ($tenants as $cliente) {
            $stats = $this->calculateStats($cliente);
            $log = ReportLog::create([
                'tenant_id' => $cliente->id,
                'periodo' => now()->subMonth()->format('m/Y'),
                'status' => 'sending'
            ]);

            try {
                Mail::send('emails.monthly-report', compact('cliente', 'stats', 'log'), function ($message) use ($cliente) {
                    $message->to($cliente->email)
                        ->subject("📊 Relatório de Desempenho: {$cliente->nome_empresa}");
                });
                $log->update(['status' => 'sent']);
            } catch (\Exception $e) {
                $log->update(['status' => 'failed']);
            }
        }

        return back()->with('success', 'Relatórios processados com sucesso!');
    }

    public function track($id)
    {
        $log = ReportLog::find($id);
        if ($log && !$log->opened_at) {
            $log->update(['opened_at' => now()]);
        }

        // Return 1x1 transparent pixel
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($pixel)->header('Content-Type', 'image/gif');
    }

    private function calculateStats(Cliente $cliente)
    {
        $start = now()->subMonth()->startOfMonth();
        $end = now()->subMonth()->endOfMonth();

        $avaliacoes = Avaliacao::where('tenant_id', $cliente->id)
            ->whereBetween('created_at', [$start, $end]);

        return [
            'total' => $avaliacoes->count(),
            'media' => round($avaliacoes->avg('nota') ?? 0, 1),
            'positivas' => $avaliacoes->clone()->where('nota', '>=', 4)->count(),
            'negativas' => $avaliacoes->clone()->where('nota', '<=', 3)->count(),
            'resolvidas' => $avaliacoes->clone()->where('nota', '<=', 3)->where('resolvido', true)->count(),
            'periodo' => $start->format('M/Y')
        ];
    }
}
