<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CP Review Care</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-br from-purple-600 to-blue-500 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8">
        <div class="text-center mb-8">
            <div class="text-5xl mb-4">⭐</div>
            <h1 class="text-2xl font-bold text-gray-800">CP Review Care</h1>
            <p class="text-gray-500 mt-1">Área Administrativa</p>
        </div>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">E-mail</label>
                <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-600" required autofocus>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Senha</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
            </div>
            <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                Entrar
            </button>
        </form>
    </div>
</body>
</html>
