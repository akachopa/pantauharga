@php
    $inputClass = 'form-field w-full bg-white border-0 ring-1 ring-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:ring-2 focus:ring-brand-500';
    $labelClass = 'block text-xs font-medium text-slate-600 mb-1.5';
@endphp
<section id="lapor-harga" class="scroll-mt-20 bg-white rounded-2xl ring-1 ring-slate-200/70 overflow-hidden grid grid-cols-1 lg:grid-cols-5">
    <div class="lg:col-span-2 bg-brand-50 p-6 sm:p-8">
        <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-ink">Kirim laporan</h2>
        <p class="text-sm text-slate-600 mt-2 leading-relaxed">Lihat harga berbeda di pasar? Kirim masukan. Angka resmi tidak berubah sebelum ditinjau petugas.</p>
        <ul class="mt-6 space-y-3 text-sm text-slate-700">
            <li class="flex gap-2.5">
                <svg class="w-5 h-5 shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                Ditinjau petugas sebelum dipakai
            </li>
            <li class="flex gap-2.5">
                <svg class="w-5 h-5 shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                Nama dan telepon tidak ditampilkan
            </li>
            <li class="flex gap-2.5">
                <svg class="w-5 h-5 shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                Maksimal 5 laporan per jam
            </li>
        </ul>
    </div>

    <form method="POST" action="{{ route('community-reports.store') }}" class="lg:col-span-3 p-6 sm:p-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        <div>
            <label class="{{ $labelClass }}">Komoditi</label>
            <select name="commodity_id" required class="{{ $inputClass }}">
                <option value="">Pilih komoditi</option>
                @foreach($commodities as $commodity)
                    <option value="{{ $commodity->id }}" @selected((string) old('commodity_id', $selectedCommodity) === (string) $commodity->id)>{{ $commodity->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">Pasar</label>
            <select name="market_id" required class="{{ $inputClass }}">
                <option value="">Pilih pasar</option>
                @foreach($markets as $market)
                    <option value="{{ $market->id }}" @selected((string) old('market_id', $selectedMarket) === (string) $market->id)>{{ $market->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">Harga yang Anda lihat (Rp)</label>
            <input type="number" inputmode="numeric" name="reported_price" value="{{ old('reported_price') }}" required min="1" class="{{ $inputClass }}" placeholder="Contoh: 16500">
        </div>
        <div>
            <label class="{{ $labelClass }}">Nama</label>
            <input type="text" name="reporter_name" value="{{ old('reporter_name') }}" required maxlength="120" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Telepon <span class="text-slate-400 font-normal">(opsional)</span></label>
            <input type="tel" inputmode="tel" name="phone" value="{{ old('phone') }}" maxlength="30" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Catatan <span class="text-slate-400 font-normal">(opsional)</span></label>
            <input type="text" name="note" value="{{ old('note') }}" maxlength="500" class="{{ $inputClass }}" placeholder="Misal: harga di kios depan pasar">
        </div>
        <div class="sm:col-span-2 pt-1">
            <button type="submit" class="w-full sm:w-auto bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors">
                Kirim laporan
            </button>
        </div>
    </form>
</section>
