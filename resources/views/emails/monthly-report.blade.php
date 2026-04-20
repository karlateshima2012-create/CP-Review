<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f9fafb; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { background: #7C3AED; color: white; padding: 40px 20px; text-align: center; }
        .content { padding: 40px; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 30px 0; }
        .stat-card { background: #f3f4f6; padding: 20px; border-radius: 12px; text-align: center; }
        .stat-value { font-size: 24px; font-weight: bold; color: #111827; }
        .stat-label { font-size: 11px; text-transform: uppercase; color: #6b7280; margin-top: 5px; }
        .footer { padding: 30px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #f3f4f6; }
        .button { display: inline-block; background: #7C3AED; color: white !important; padding: 14px 30px; text-decoration: none; border-radius: 10px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0; font-size: 24px;">Relatório Mensal</h1>
            <p style="opacity: 0.8; margin-top: 10px;">{{ $stats['periodo'] }} • {{ $cliente->nome_empresa }}</p>
        </div>
        
        <div class="content">
            <h2 style="color: #111827;">Olá, {{ $cliente->nome_empresa }}!</h2>
            <p style="color: #4b5563; line-height: 1.6;">Aqui está o resumo da performance do seu estabelecimento no último mês através do CP Review.</p>
            
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="50%" style="padding: 10px;">
                        <div style="background: #f3f4f6; padding: 20px; border-radius: 12px; text-align: center;">
                            <div style="font-size: 24px; font-weight: bold; color: #111827;">{{ $stats['total'] }}</div>
                            <div style="font-size: 11px; text-transform: uppercase; color: #6b7280; margin-top: 5px;">Avaliações Totais</div>
                        </div>
                    </td>
                    <td width="50%" style="padding: 10px;">
                        <div style="background: #f3f4f6; padding: 20px; border-radius: 12px; text-align: center;">
                            <div style="font-size: 24px; font-weight: bold; color: #7C3AED;">{{ $stats['media'] }} ★</div>
                            <div style="font-size: 11px; text-transform: uppercase; color: #6b7280; margin-top: 5px;">Média Global</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding: 10px;">
                        <div style="background: #f0fdf4; padding: 20px; border-radius: 12px; text-align: center;">
                            <div style="font-size: 24px; font-weight: bold; color: #166534;">{{ $stats['positivas'] }}</div>
                            <div style="font-size: 11px; text-transform: uppercase; color: #166534; margin-top: 5px;">Elogios</div>
                        </div>
                    </td>
                    <td width="50%" style="padding: 10px;">
                        <div style="background: #fef2f2; padding: 20px; border-radius: 12px; text-align: center;">
                            <div style="font-size: 24px; font-weight: bold; color: #991b1b;">{{ $stats['negativas'] }}</div>
                            <div style="font-size: 11px; text-transform: uppercase; color: #991b1b; margin-top: 5px;">Críticas</div>
                        </div>
                    </td>
                </tr>
            </table>

            <div style="margin-top: 30px; padding: 20px; border: 1px dashed #e5e7eb; border-radius: 12px; text-align: center;">
                <p style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">Você resolveu <strong>{{ $stats['resolvidas'] }}</strong> das críticas recebidas.</p>
                <p style="color: #111827; font-size: 14px; font-weight: bold;">Continue ouvindo seus clientes para crescer mais!</p>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/cliente/' . $cliente->id) }}" class="button">Acessar Painel Completo</a>
            </div>
        </div>

        <div class="footer">
            Este é um relatório automático gerado pelo CP Review Care.<br>
            © {{ date('Y') }} CP Review • Todos os direitos reservados.
        </div>
    </div>
    @if(isset($log))
    <img src="{{ url('/admin/reports/track/' . $log->id) }}" width="1" height="1" style="display:none;">
    @endif
</body>
</html>
