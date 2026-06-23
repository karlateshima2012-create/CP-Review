@extends('layouts.cliente')

@section('title', 'Divulgação - CP Review')

@section('cliente_content')
<!-- Page Header -->
<div class="mb-32">
    <h2 class="text-title-1 font-bold text-neutral-primary">{{ __('Divulgação') }}</h2>
    <p class="text-body-m text-neutral-secondary">{{ __('Compartilhe para receber avaliações e gerencie sua reputação') }}</p>
</div>

<div class="grid lg:grid-cols-12 gap-16 lg:gap-32 items-start">

    <!-- Left Column: QR Code -->
    <div class="lg:col-span-6 card p-24 flex flex-col items-center text-center">
        <!-- QR Code Image Box -->
        <div class="border border-neutral-border rounded-xl p-16 bg-white shadow-sm mb-12 flex items-center justify-center w-[220px] h-[220px]">
            <img src="{{ url("/cliente/qrcode/{$cliente->id}") }}" alt="QR Code" class="w-full h-full object-contain">
        </div>

        <!-- Dica logo abaixo do QR -->
        <p class="text-body-m text-neutral-secondary leading-relaxed mb-24 px-8">
            {{ __('Imprima e cole na mesa, balcão ou cardápio. O cliente escaneia, avalia — satisfeito vai ao Google, insatisfeito fica em Ocorrências.') }}
        </p>

        <!-- Evaluation link display -->
        <div class="bg-neutral-bg border border-neutral-border rounded-lg px-16 py-12 mb-24 w-full text-center">
            <span class="text-legend text-neutral-secondary font-bold uppercase tracking-wider block mb-4">{{ __('Link de avaliação') }}</span>
            <div id="eval-message" class="text-body-m text-neutral-primary leading-relaxed break-all">
                {{ __('Olá! Por favor, deixe sua avaliação sobre nós em:') }}
                <a href="{{ route('avaliacao.show', ['slug' => $cliente->slug, 'v' => 5]) }}" id="eval-link" class="font-bold text-brand-600 hover:underline" target="_blank">
                    {{ route('avaliacao.show', ['slug' => $cliente->slug, 'v' => 5]) }}
                </a>
            </div>
        </div>

        <div class="w-full space-y-12 mt-auto">
            <a href="{{ url("/cliente/qrcode/{$cliente->id}/download") }}" class="w-full bg-brand-600 text-white py-12 rounded-lg font-bold hover:bg-brand-700 transition flex items-center justify-center gap-8 shadow-sm">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"></path></svg>
                {{ __('Baixar QR Code') }}
            </a>
            <button onclick="copyToClipboard()" class="w-full border border-neutral-border bg-white text-neutral-primary hover:bg-neutral-bg py-12 rounded-lg font-bold transition flex items-center justify-center gap-8">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 1 1.242 7.244" />
                </svg>
                {{ __('Copiar link') }}
            </button>
        </div>
    </div>

    <!-- Right Column -->
    <div class="lg:col-span-6 flex flex-col gap-16">

        <!-- Google Reviews Card -->
        <div class="card p-16">
            <div class="flex items-center gap-10 mb-12">
                <svg class="w-20 h-20 flex-shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.4-1.04 2.58-2.23 3.37v2.79h3.61c2.11-1.95 3.26-4.82 3.26-8.17z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.61-2.79c-.98.66-2.23 1.06-3.67 1.06-2.82 0-5.21-1.9-6.07-4.47H2.18v2.87C4.01 20.07 7.77 23 12 23z" fill="#34A853"/>
                    <path d="M5.93 14.14A7.01 7.01 0 0 1 5.56 12c0-.74.13-1.46.37-2.14V7.99H2.18A11.01 11.01 0 0 0 1 12c0 1.79.43 3.48 1.18 4.99l3.75-2.85z" fill="#FBBC05"/>
                    <path d="M12 5.04c1.59 0 3.01.55 4.13 1.62l3.08-3.08C17.46 1.96 14.97 1 12 1 7.77 1 4.01 3.93 2.18 8.01l3.75 2.85C6.79 6.94 9.18 5.04 12 5.04z" fill="#EA4335"/>
                </svg>
                <div>
                    <span class="text-body-m font-bold text-neutral-primary block leading-tight">{{ __('Avaliações do Google') }}</span>
                    <span class="text-legend text-neutral-secondary">{{ __('Responda publicamente pelo Perfil da Empresa') }}</span>
                </div>
            </div>

            <div class="space-y-8">
                <p class="text-legend text-neutral-secondary leading-relaxed">
                    {{ __('⚠️ Você precisa estar logado com a conta Google associada ao seu negócio.') }}
                </p>
                <a href="https://business.google.com/reviews" target="_blank"
                   class="w-full flex items-center justify-center gap-8 py-10 px-14 rounded-lg font-bold text-white text-body-m transition shadow-sm"
                   style="background: linear-gradient(135deg, #4285F4 0%, #34A853 100%);">
                    <svg class="w-14 h-14" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/>
                    </svg>
                    {{ __('Responder avaliações no Google') }}
                    <svg class="w-12 h-12 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                    </svg>
                </a>

                @if($cliente->google_maps_link)
                <a href="{{ $cliente->google_maps_link }}" target="_blank"
                   class="w-full flex items-center justify-center gap-8 py-8 px-14 rounded-lg border border-neutral-border bg-white text-neutral-secondary hover:bg-neutral-bg transition text-body-m font-bold">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/>
                    </svg>
                    {{ __('Ver perfil no Google Maps') }}
                </a>
                @endif
            </div>
        </div>

        <!-- Sharing Card -->
        <div class="card p-16">
            <span class="text-legend text-neutral-secondary font-bold uppercase tracking-wider block mb-12">{{ __('Compartilhar link de avaliação') }}</span>

            <div class="grid grid-cols-3 gap-10">
                <a href="https://api.whatsapp.com/send?text=Olá!%20Por%20favor,%20deixe%20sua%20avaliação%20sobre%20nós%20em:%20{{ urlencode(route('avaliacao.show', ['slug' => $cliente->slug, 'v' => 5])) }}" target="_blank"
                   class="border border-neutral-border bg-white hover:bg-neutral-bg py-12 px-8 rounded-lg text-body-m font-bold text-neutral-primary flex items-center justify-center transition text-center">
                    WhatsApp
                </a>
                <a href="https://line.me/R/share?text=Olá!%20Por%20favor,%20deixe%20sua%20avaliação%20sobre%20nós%20em:%20{{ urlencode(route('avaliacao.show', ['slug' => $cliente->slug, 'v' => 5])) }}" target="_blank"
                   class="border border-neutral-border bg-white hover:bg-neutral-bg py-12 px-8 rounded-lg text-body-m font-bold text-neutral-primary flex items-center justify-center transition text-center">
                    LINE
                </a>
                <a href="mailto:?subject=Deixe%20sua%20avaliação&body=Olá!%20Por%20favor,%20deixe%20sua%20avaliação%20sobre%20nós%20em:%20{{ route('avaliacao.show', ['slug' => $cliente->slug, 'v' => 5]) }}"
                   class="border border-neutral-border bg-white hover:bg-neutral-bg py-12 px-8 rounded-lg text-body-m font-bold text-neutral-primary flex items-center justify-center transition text-center">
                    {{ __('E-mail') }}
                </a>
            </div>
        </div>

    </div>
</div>

<!-- Toast -->
<div id="toast" class="fixed bottom-48 right-16 bg-neutral-primary text-white py-8 px-16 rounded-lg shadow-lg text-body-m font-medium transition opacity-0 pointer-events-none transform translate-y-8 z-50">
    Link copiado com sucesso!
</div>

<script>
    function copyToClipboard() {
        const messageText = "{{ __('Olá! Por favor, deixe sua avaliação sobre nós em:') }} " + document.getElementById('eval-link').href;
        navigator.clipboard.writeText(messageText).then(() => {
            const toast = document.getElementById('toast');
            toast.innerText = "{{ __('Mensagem copiada com sucesso!') }}";
            toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-8');
            toast.classList.add('opacity-100', 'translate-y-0');
            setTimeout(() => {
                toast.classList.add('opacity-0', 'pointer-events-none', 'translate-y-8');
                toast.classList.remove('opacity-100', 'translate-y-0');
            }, 2000);
        });
    }
</script>
@endsection
