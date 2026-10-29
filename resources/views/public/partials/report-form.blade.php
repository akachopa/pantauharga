<section id="lapor-harga" class="scroll-mt-20 bg-white rounded-lg border border-slate-300 grid grid-cols-1 lg:grid-cols-5">
    <div class="lg:col-span-2 p-4 sm:p-5 border-b lg:border-b-0 lg:border-r border-slate-200 bg-amber-50/60">
        <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Lapor Harga</h2>
        <p class="text-sm text-slate-700 mt-1">Lihat harga berbeda di pasar? Kirim masukan. Angka resmi tidak berubah sebelum ditinjau petugas.</p>
        <p class="text-xs text-slate-500 mt-3">Laporan Anda akan dicek petugas dan dibandingkan dengan data lapangan. Data pribadi tidak ditampilkan ke publik.</p>
    </div>
    <form method="POST" action="{{ route('community-reports.store') }}" class="lg:col-span-3 p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        @csrf
        <div>
            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Komoditi</label>
            <select name="commodity_id" required class="form-field w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-brand-700 focus:ring-brand-700">
                <option value="">Pilih komoditi</option>
                @foreach($commodities as $commodity)
                    <option value="{{ $commodity->id }}" @selected((string) old('commodity_id', $nearbyCommodityId) === (string) $commodity->id)>{{ $commodity->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Pasar</label>
            <select name="market_id" required class="form-field w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-brand-700 focus:ring-brand-700">
                <option value="">Pilih pasar</option>
                @foreach($markets as $market)
                    <option value="{{ $market->id }}" @selected((string) old('market_id', $selectedMarket) === (string) $market->id)>{{ $market->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Harga yang Anda lihat (Rp)</label>
            <input type="number" inputmode="numeric" name="reported_price" value="{{ old('reported_price') }}" required min="1" class="form-field w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-brand-700 focus:ring-brand-700" placeholder="Contoh: 16500">
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Nama</label>
            <input type="text" name="reporter_name" value="{{ old('reporter_name') }}" required maxlength="120" class="form-field w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-brand-700 focus:ring-brand-700">
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Telepon (opsional)</label>
            <input type="tel" inputmode="tel" name="phone" value="{{ old('phone') }}" maxlength="30" class="form-field w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-brand-700 focus:ring-brand-700">
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Catatan (opsional)</label>
            <input type="text" name="note" value="{{ old('note') }}" maxlength="500" class="form-field w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-brand-700 focus:ring-brand-700" placeholder="Misal: harga di kios depan pasar">
        </div>
        <div class="sm:col-span-2 flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-3 pt-1">
            <p class="text-xs text-slate-400">Maksimal 5 laporan per jam dari perangkat yang sama.</p>
            <button type="submit" class="w-full sm:w-auto bg-brand-800 hover:bg-brand-900 text-white font-semibold text-sm px-5 py-2.5 rounded-md transition-colors">
                Kirim laporan
            </button>
        </div>
    </form>
</section>
