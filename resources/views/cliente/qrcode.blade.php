@extends('layouts.cliente')

@section('title', 'QR Code e Link - CP Review')

@section('cliente_content')
<!-- Page Header -->
<div class="mb-32">
    <h2 class="text-title-1 font-bold text-neutral-primary">QR Code e Link</h2>
    <p class="text-body-m text-neutral-secondary">Compartilhe para receber avaliações</p>
</div>

<div class="grid lg:grid-cols-12 gap-32 items-start">
    <!-- Left Column: QR Code Display -->
    <div class="lg:col-span-6 card p-24 flex flex-col items-center text-center">
        <!-- QR Code Image Box -->
        <div class="border border-neutral-border rounded-xl p-16 bg-white shadow-sm mb-24 flex items-center justify-center w-[220px] h-[220px]">
            <img src="{{ url("/cliente/qrcode/{$cliente->id}") }}" alt="QR Code" class="w-full h-full object-contain">
        </div>

        <!-- Evaluation link display -->
        <div class="bg-neutral-bg border border-neutral-border rounded-lg px-16 py-12 mb-24 w-full text-center">
            <span class="text-legend text-neutral-secondary font-bold uppercase tracking-wider block mb-4">Link de avaliação</span>
            <div id="eval-message" class="text-body-m text-neutral-primary leading-relaxed break-all">
                Olá! Por favor, deixe sua avaliação sobre nós em: 
                <a href="{{ route('avaliacao.show', ['slug' => $cliente->slug, 'v' => 5]) }}" id="eval-link" class="font-bold text-brand-600 hover:underline" target="_blank">
                    {{ route('avaliacao.show', ['slug' => $cliente->slug, 'v' => 5]) }}
                </a>
            </div>
        </div>

        <div class="w-full space-y-12">
            <!-- Download Button -->
            <a href="{{ url("/cliente/qrcode/{$cliente->id}/download") }}" class="w-full bg-brand-600 text-white py-12 rounded-lg font-bold hover:bg-brand-700 transition flex items-center justify-center gap-8 shadow-sm">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"></path></svg>
                Baixar QR Code
            </a>

            <!-- Copy Link Button -->
            <button onclick="copyToClipboard()" class="w-full border border-neutral-border bg-white text-neutral-primary hover:bg-neutral-bg py-12 rounded-lg font-bold transition flex items-center justify-center gap-8">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H5.25m11.9-3.664A2.25 2.25 0 0 0 15 2.25h-1.5a2.25 2.25 0 0 0-2.25 2.25v1.5a2.25 2.25 0 0 0 2.25 2.25H15a2.25 2.25 0 0 0 2.25-2.25V5.25z"></path></svg>
                Copiar link
            </button>
        </div>
    </div>

    <!-- Right Column: Sharing & Guide -->
    <div class="lg:col-span-6 space-y-24">
        
        <!-- Sharing Card -->
        <div class="card p-24">
            <span class="text-legend text-neutral-secondary font-bold uppercase tracking-wider block mb-16">Compartilhar</span>
            
            <div class="grid grid-cols-3 gap-12">
                <!-- WhatsApp -->
                <a href="https://api.whatsapp.com/send?text=Olá!%20Por%20favor,%20deixe%20sua%20avaliação%20sobre%20nós%20em:%20{{ urlencode(route('avaliacao.show', ['slug' => $cliente->slug, 'v' => 5])) }}" target="_blank" class="border border-neutral-border bg-white hover:bg-neutral-bg py-12 px-8 rounded-lg text-body-m font-bold text-neutral-primary flex items-center justify-center transition text-center">
                    WhatsApp
                </a>
                <!-- LINE -->
                <a href="https://line.me/R/share?text=Olá!%20Por%20favor,%20deixe%20sua%20avaliação%20sobre%20nós%20em:%20{{ urlencode(route('avaliacao.show', ['slug' => $cliente->slug, 'v' => 5])) }}" target="_blank" class="border border-neutral-border bg-white hover:bg-neutral-bg py-12 px-8 rounded-lg text-body-m font-bold text-neutral-primary flex items-center justify-center transition text-center">
                    LINE
                </a>
                <!-- Email -->
                <a href="mailto:?subject=Deixe%20sua%20avaliação&body=Olá!%20Por%20favor,%20deixe%20sua%20avaliação%20sobre%20nós%20em:%20{{ route('avaliacao.show', ['slug' => $cliente->slug, 'v' => 5]) }}" class="border border-neutral-border bg-white hover:bg-neutral-bg py-12 px-8 rounded-lg text-body-m font-bold text-neutral-primary flex items-center justify-center transition text-center">
                    E-mail
                </a>
            </div>
        </div>

        <!-- How to use Card -->
        <div class="card p-24 space-y-16">
            <span class="text-legend text-neutral-secondary font-bold uppercase tracking-wider block">Como usar</span>
            
            <p class="text-body-m text-neutral-secondary leading-relaxed">
                Imprima e cole na mesa, balcão ou cardápio. O cliente escaneia, avalia — satisfeito vai ao Google, insatisfeito fica registrado em Ocorrências.
            </p>
        </div>

    </div>
</div>

<!-- Alert Banner to show when link is copied -->
<div id="toast" class="fixed bottom-48 right-16 bg-neutral-primary text-white py-8 px-16 rounded-lg shadow-lg text-body-m font-medium transition opacity-0 pointer-events-none transform translate-y-8 z-50">
    Link copiado com sucesso!
</div>

<script>
    function copyToClipboard() {
        const messageText = "Olá! Por favor, deixe sua avaliação sobre nós em: " + document.getElementById('eval-link').href;
        navigator.clipboard.writeText(messageText).then(() => {
            const toast = document.getElementById('toast');
            toast.innerText = "Mensagem copiada com sucesso!";
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
