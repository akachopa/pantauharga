@php
    $exploreLinks = [
        ['route' => 'public.markets', 'title' => 'Pasar Terdekat', 'text' => 'Cari pasar terdekat dan termurah dari lokasi Anda.', 'icon' => 'bg-sky-50 text-sky-600', 'path' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
        ['route' => 'public.trends', 'title' => 'Tren', 'text' => 'Grafik 7/30 hari dan insight otomatis kondisi harga.', 'icon' => 'bg-rose-50 text-rose-600', 'path' => 'M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941'],
        ['route' => 'public.table', 'title' => 'Tabel', 'text' => 'Rincian harga dan stok di setiap pasar.', 'icon' => 'bg-slate-100 text-slate-600', 'path' => 'M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h17.25c.621 0 1.125-.504 1.125-1.125M2.25 18.375V5.625m0 0c0-.621.504-1.125 1.125-1.125h17.25c.621 0 1.125.504 1.125 1.125M2.25 5.625v12.75m19.5-12.75v12.75M2.25 9.75h19.5M2.25 14.25h19.5M9 4.5v15'],
        ['route' => 'public.report', 'title' => 'Lapor Harga', 'text' => 'Lihat harga berbeda? Kirim masukan ke petugas.', 'icon' => 'bg-amber-50 text-amber-600', 'path' => 'M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z'],
    ];
@endphp
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    @foreach($exploreLinks as $link)
        <a href="{{ route($link['route'], $link['route'] === 'public.report' ? [] : $publicFilterQuery) }}"
           class="group bg-white rounded-2xl ring-1 ring-slate-200/70 hover:ring-brand-300 p-5 flex sm:flex-col gap-4 transition">
            <span class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $link['icon'] }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $link['path'] }}"/></svg>
            </span>
            <span class="min-w-0">
                <span class="flex items-center gap-1.5 font-display font-bold text-ink">
                    {{ $link['title'] }}
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </span>
                <span class="block mt-1 text-sm text-slate-500">{{ $link['text'] }}</span>
            </span>
        </a>
    @endforeach
</div>
