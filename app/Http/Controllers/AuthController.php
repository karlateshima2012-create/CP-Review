<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
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
            }

            // 5. Clear session support mode
            session()->forget('impersonate_tenant_id');
        } catch (\Exception $e) {
            // Silence any issues so the login page doesn't crash
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect('/admin');
            }

            // Para lojistas, redireciona para o dashboard deles
            $tenantId = Auth::user()->tenant_id;
            if ($tenantId) {
                return redirect(route('cliente.dashboard', ['cliente' => $tenantId]));
            }

            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
