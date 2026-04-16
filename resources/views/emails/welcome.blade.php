<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao CP Review Care</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
            <div style="font-size: 48px;">⭐</div>
            <h1 style="color: white; margin: 10px 0 0;">CP Review Care</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0;">Seu sistema de avaliações está ativo!</p>
        </div>

        <div style="padding: 30px;">
            <h2 style="color: #333; margin-top: 0;">Olá {{ $cliente->nome_empresa }}!</h2>
            <p style="color: #666; line-height: 1.6;">Seu sistema de avaliações foi ativado com sucesso. Agora você pode começar a coletar feedback dos seus clientes.</p>

            <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                <h3 style="margin: 0 0 15px; color: #333;">📱 Seus Links Importantes:</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    <li style="margin: 10px 0;">
                        <strong>Painel do Cliente:</strong><br>
                        <a href="{{ $linkPainel }}" style="color: #667eea; word-break: break-all;">{{ $linkPainel }}</a>
                    </li>
                    <li style="margin: 10px 0;">
                        <strong>Link de Avaliação:</strong><br>
                        <a href="{{ $linkAvaliacao }}" style="color: #667eea; word-break: break-all;">{{ $linkAvaliacao }}</a>
                    </li>
                    <li style="margin: 10px 0;">
                        <strong>QR Code:</strong><br>
                        <a href="{{ $linkQR }}" style="color: #667eea;">Clique aqui para baixar</a>
                    </li>
                </ul>
            </div>

            <div style="background: #e8f5e9; border-radius: 8px; padding: 20px; margin: 20px 0;">
                <h3 style="margin: 0 0 10px; color: #2e7d32;">📋 Instruções Rápidas:</h3>
                <ol style="margin: 0; padding-left: 20px; color: #555;">
                    <li style="margin: 5px 0;">Imprima o QR Code e coloque em local visível (balcão, caixa, mesa)</li>
                    <li style="margin: 5px 0;">Peça para seus clientes escanearem e avaliarem</li>
                    <li style="margin: 5px 0;">Acompanhe as avaliações pelo seu painel</li>
                    <li style="margin: 5px 0;">Responda avaliações negativas diretamente pelo painel</li>
                </ol>
            </div>

            <p style="color: #666; margin: 20px 0 0;">Qualquer dúvida, estamos à disposição.</p>
            <p style="color: #666; margin: 10px 0 0;">Atenciosamente,<br><strong>Equipe CP Review Care</strong></p>
        </div>

        <div style="background: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #999;">
            <p>Este é um e-mail automático. Por favor, não responda.</p>
            <p>&copy; {{ date('Y') }} CP Review Care. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
