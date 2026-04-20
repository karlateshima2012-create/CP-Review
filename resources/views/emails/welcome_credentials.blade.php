<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; padding: 40px 0; }
        .card { max-width: 500px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        .header { background: #7C3AED; color: white; padding: 60px 40px; text-align: center; }
        .body { padding: 40px; }
        .cred-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .label { font-size: 10px; font-weight: 800; text-transform: uppercase; color: #9ca3af; margin-bottom: 5px; }
        .val { font-family: 'Courier New', monospace; font-weight: bold; color: #111827; }
        .btn { display: block; background: #7C3AED; color: white !important; text-align: center; padding: 16px; border-radius: 12px; text-decoration: none; font-weight: bold; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1 style="margin:0; font-size: 28px;">Seja Bem-vindo!</h1>
            <p style="opacity: 0.8; margin-top: 10px;">{{ $cliente->nome_empresa }}</p>
        </div>
        <div class="body">
            <p style="color: #4b5563; line-height: 1.6;">O seu sistema de inteligência de experiência do cliente já está pronto. Use as credenciais abaixo para acessar seu painel administrativo:</p>
            
            <div class="cred-box">
                <div class="label">E-mail de Acesso</div>
                <div class="val">{{ $user->email }}</div>
                <div style="height: 15px;"></div>
                <div class="label">Senha Temporária</div>
                <div class="val">{{ $password }}</div>
            </div>

            <p style="font-size: 13px; color: #6b7280;">* Por segurança, altere sua senha no primeiro acesso.</p>

            <a href="{{ url('/login') }}" class="btn">Acessar Meu Painel</a>

            <div style="margin-top: 40px; border-top: 1px solid #f3f4f6; pt: 30px; text-align: center;">
                <p style="font-size: 11px; color: #9ca3af;">CP Review Care • Inteligência em Customer Success</p>
            </div>
        </div>
    </div>
</body>
</html>
