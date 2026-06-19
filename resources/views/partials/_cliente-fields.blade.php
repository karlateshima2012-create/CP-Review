{{--
  Partial: _cliente-fields.blade.php
  Campos compartilhados entre admin/create e landing/contratar.
  Uso: @include('partials._cliente-fields', ['defaults' => $array])
--}}
@php
$d = $defaults ?? [];
$currentPack  = old('pack_idioma',       $d['pack_idioma']       ?? 'pt_ja');
$currentCanal = old('canal_notificacao', $d['canal_notificacao'] ?? ($currentPack === 'ja_en' ? 'line' : 'whatsapp'));
$fieldClass   = 'w-full px-5 py-4 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all font-medium text-gray-900 text-sm';
$labelClass   = 'block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2';
@endphp

{{-- 01: Pack de Idiomas --}}
<div class="space-y-3">
    <label class="{{ $labelClass }}">Pacote de Idiomas do Bot</label>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        @foreach([
            [
                'value' => 'pt_ja',
                'flags' => '🇧🇷 + 🇯🇵',
                'lang'  => 'Português + 日本語',
                'desc'  => 'Para brasileiros atendendo clientes japoneses',
            ],
            [
                'value' => 'ja_en',
                'flags' => '🇯🇵 + 🇺🇸',
                'lang'  => '日本語 + English',
                'desc'  => 'Para japoneses atendendo clientes estrangeiros',
            ],
        ] as $pack)
        <label for="cf-pack-{{ $pack['value'] }}" class="cursor-pointer group">
            <input
                type="radio"
                id="cf-pack-{{ $pack['value'] }}"
                name="pack_idioma"
                value="{{ $pack['value'] }}"
                {{ $currentPack === $pack['value'] ? 'checked' : '' }}
                onchange="cfPackChange(this)"
                class="sr-only peer">
            <div class="border-2 rounded-2xl p-5 transition-all select-none
                        peer-checked:border-purple-500 peer-checked:bg-purple-50
                        border-gray-200 group-hover:border-gray-300 bg-white">
                <div class="text-3xl mb-3">{{ $pack['flags'] }}</div>
                <p class="font-bold text-sm text-gray-900">{{ $pack['lang'] }}</p>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $pack['desc'] }}</p>
            </div>
        </label>
        @endforeach

    </div>
    {{-- pais é derivado do pack no controller, campo hidden para compatibilidade --}}
    <input type="hidden" name="pais" id="cf-pais" value="{{ $currentPack === 'ja_en' ? 'jp' : 'br' }}">
</div>

{{-- 02: Nome + E-mail --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="space-y-2">
        <label class="{{ $labelClass }}">Nome da Empresa</label>
        <input
            type="text"
            name="nome_empresa"
            value="{{ old('nome_empresa', $d['nome_empresa'] ?? '') }}"
            placeholder="Ex: Sakura Restaurant"
            required
            class="{{ $fieldClass }}">
    </div>
    <div class="space-y-2">
        <label class="{{ $labelClass }}">E-mail de Acesso (Login)</label>
        <input
            type="email"
            name="email"
            value="{{ old('email', $d['email'] ?? '') }}"
            placeholder="contato@empresa.com"
            required
            class="{{ $fieldClass }}">
    </div>
</div>

{{-- 03: Canal + Contato --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="space-y-2">
        <label class="{{ $labelClass }}">Canal de Notificação</label>
        <select
            name="canal_notificacao"
            id="cf-canal"
            onchange="cfCanalChange(this)"
            class="{{ $fieldClass }} appearance-none">
            <option value="whatsapp" {{ $currentCanal === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
            <option value="line"     {{ $currentCanal === 'line'     ? 'selected' : '' }}>LINE</option>
        </select>
    </div>

    <div id="cf-wrap-wa" class="{{ $currentCanal !== 'whatsapp' ? 'hidden' : '' }} space-y-2">
        <label class="{{ $labelClass }}">Número WhatsApp</label>
        <input
            type="text"
            name="telefone_whatsapp"
            value="{{ old('telefone_whatsapp', $d['telefone_whatsapp'] ?? '') }}"
            placeholder="5511999999999"
            class="{{ $fieldClass }}">
    </div>

    <div id="cf-wrap-line" class="{{ $currentCanal !== 'line' ? 'hidden' : '' }} space-y-2">
        <label class="{{ $labelClass }}">LINE User ID</label>
        <input
            type="text"
            name="line_user_id"
            value="{{ old('line_user_id', $d['line_user_id'] ?? '') }}"
            placeholder="Uxxxxxxxxxxxxxxxx"
            class="{{ $fieldClass }}">
    </div>
</div>

{{-- 04: Google Maps --}}
<div class="space-y-2">
    <label class="{{ $labelClass }}">Link Google Maps (Review)</label>
    <input
        type="url"
        name="google_maps_link"
        value="{{ old('google_maps_link', $d['google_maps_link'] ?? '') }}"
        placeholder="https://g.page/r/..."
        class="{{ $fieldClass }}">
    <p class="text-xs text-gray-400 mt-1">
        Google Maps → seu estabelecimento → Compartilhar → Copiar link
    </p>
</div>

<script>
if (typeof cfPackChange === 'undefined') {
    function cfPackChange(radio) {
        var isJaEn = radio.value === 'ja_en';
        var pais = document.getElementById('cf-pais');
        if (pais) pais.value = isJaEn ? 'jp' : 'br';
        var canal = document.getElementById('cf-canal');
        if (canal) { canal.value = isJaEn ? 'line' : 'whatsapp'; cfCanalChange(canal); }
    }
    function cfCanalChange(select) {
        var wa = document.getElementById('cf-wrap-wa');
        var ln = document.getElementById('cf-wrap-line');
        var isWa = select.value === 'whatsapp';
        if (wa) wa.classList.toggle('hidden', !isWa);
        if (ln) ln.classList.toggle('hidden', isWa);
    }
}
</script>
