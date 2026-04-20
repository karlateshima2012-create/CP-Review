@extends('layouts.admin')

@section('title', 'Gerador de QR Code Pro')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 no-print">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Configurações (Lado Esquerdo) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    🎨 Personalização
                </h3>
                
                <form action="{{ route('admin.clientes.qrcode.branding', $cliente->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Cor do QR Code</label>
                        <div class="flex gap-2">
                            <input type="color" name="qr_color" id="qr_color_input" value="{{ $cliente->qr_color ?: '#7C3AED' }}" class="w-12 h-10 rounded-lg cursor-pointer border-none p-0">
                            <input type="text" id="qr_color_text" value="{{ $cliente->qr_color ?: '#7C3AED' }}" class="flex-1 rounded-lg border-gray-200 text-sm font-mono uppercase">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Logo Central (PNG)</label>
                        <input type="file" name="qr_logo" id="qr_logo_input" accept="image/png" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    </div>

                    <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-xl font-bold hover:bg-gray-800 transition shadow-lg">
                        Salvar Ajustes
                    </button>
                </form>
            </div>

            <!-- Histórico -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">📜 Histórico de Edições</h3>
                <div class="space-y-3">
                    @foreach($historico as $log)
                    <div class="text-[10px] text-gray-500 border-l-2 border-purple-200 pl-3">
                        <div class="font-bold text-gray-700">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                        <div>{{ $log->details }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Preview (Centro) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-8 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900">{{ $cliente->nome_empresa }}</h2>
                        <p class="text-sm text-gray-500">Preview em tempo real do material impresso</p>
                    </div>
                    <div class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-tighter">
                        Link Ativo: /r/{{ $cliente->slug }}
                    </div>
                </div>

                <div class="p-12 flex flex-col items-center">
                    <!-- Área do QR Code Renderizado -->
                    <div id="qr-container" class="relative p-6 bg-white rounded-3xl shadow-2xl border-2 border-gray-50">
                        <div id="qrcode"></div>
                        @if($cliente->qr_logo_path)
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-16 h-16 bg-white rounded-xl shadow-lg p-2 overflow-hidden border border-gray-100">
                                <img src="{{ asset('storage/' . $cliente->qr_logo_path) }}" alt="Logo" class="w-full h-full object-contain">
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">
                        <button onclick="downloadPNG()" class="flex flex-col items-center justify-center gap-2 bg-blue-50 text-blue-700 p-4 rounded-2xl hover:bg-blue-100 transition border border-blue-100">
                            <span class="text-2xl">🖼️</span>
                            <span class="text-xs font-bold uppercase">PNG Digital</span>
                        </button>
                        <button onclick="printFormat('A4')" class="flex flex-col items-center justify-center gap-2 bg-purple-50 text-purple-700 p-4 rounded-2xl hover:bg-purple-100 transition border border-purple-100">
                            <span class="text-2xl">📄</span>
                            <span class="text-xs font-bold uppercase">PDF A4 (Print)</span>
                        </button>
                        <button onclick="printFormat('A5')" class="flex flex-col items-center justify-center gap-2 bg-indigo-50 text-indigo-700 p-4 rounded-2xl hover:bg-indigo-100 transition border border-indigo-100">
                            <span class="text-2xl">🎴</span>
                            <span class="text-xs font-bold uppercase">PDF A5 (Mesa)</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <a href="{{ route('admin.clientes') }}" class="text-sm font-medium text-gray-400 hover:text-purple-600 transition">
                    ← Voltar para todos os tenants
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Estilos para Impressão -->
<div id="print-area" class="hidden print:block bg-white h-screen">
    <div class="flex flex-col items-center justify-center h-full text-center space-y-12">
        <h1 class="text-6xl font-black text-gray-900">{{ $cliente->nome_empresa }}</h1>
        <div id="print-qrcode" class="scale-[2.5] origin-center"></div>
        <div class="space-y-4">
            <p class="text-4xl font-bold text-gray-600">Sua opinião é fundamental!</p>
            <p class="text-3xl font-medium text-gray-400 tracking-wide">Escaneie para avaliar o nosso atendimento</p>
        </div>
        <div class="pt-20">
            <p class="text-2xl font-mono text-gray-300">Powered by CP Review Care</p>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    #print-area { display: block !important; }
    
    @page {
        margin: 0;
    }
    
    .print-a4 { width: 210mm; height: 297mm; }
    .print-a5 { width: 148mm; height: 210mm; }
}

#qrcode canvas, #qrcode img {
    border-radius: 1rem;
}
</style>

<!-- QRCode JavaScript Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/easyqrcodejs/4.4.10/easy.qrcode.min.js"></script>
<script>
    const qrColor = "{{ $cliente->qr_color ?: '#7C3AED' }}";
    const qrUrl = "{{ url('/r/' . $cliente->slug) }}";
    const qrLogo = @if($cliente->qr_logo_path) "{{ asset('storage/' . $cliente->qr_logo_path) }}" @else null @endif;

    let qrcode = new QRCode(document.getElementById("qrcode"), {
        text: qrUrl,
        width: 300,
        height: 300,
        colorDark: qrColor,
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H,
        quietZone: 20,
        logo: qrLogo,
        logoWidth: 80,
        logoHeight: 80,
        logoBackgroundTransparent: true
    });

    let printQrcode = new QRCode(document.getElementById("print-qrcode"), {
        text: qrUrl,
        width: 400,
        height: 400,
        colorDark: qrColor,
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H,
        logo: qrLogo,
        logoWidth: 100,
        logoHeight: 100
    });

    // Sync input color
    document.getElementById('qr_color_input').addEventListener('input', (e) => {
        const color = e.target.value;
        document.getElementById('qr_color_text').value = color;
        // Update Preview
        updateQR(color);
    });

    function updateQR(color) {
        qrcode.clear();
        qrcode.makeCode(qrUrl, { colorDark: color });
        
        printQrcode.clear();
        printQrcode.makeCode(qrUrl, { colorDark: color });
    }

    function downloadPNG() {
        const canvas = document.querySelector("#qrcode canvas");
        const link = document.createElement("a");
        link.download = "QR_Code_{{ $cliente->slug }}.png";
        link.href = canvas.toDataURL();
        link.click();
    }

    function printFormat(format) {
        document.getElementById('print-area').className = 'hidden print:block bg-white print-' + format.toLowerCase();
        window.print();
    }
</script>
@endsection
