<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.partials.public-head')
</head>
<body class="min-h-screen flex flex-col antialiased">
    <header class="bg-white/95 backdrop-blur border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 min-w-0">
                <img src="{{ asset('images/lambang-bengkulu-tengah.jpg') }}" alt="Lambang Kabupaten Bengkulu Tengah" class="h-10 w-auto shrink-0" width="30" height="40">
                <span class="min-w-0 leading-tight">
                    <span class="block font-display font-extrabold text-ink text-base uppercase tracking-wide">Pantau Harga</span>
                    <span class="block text-xs text-slate-500 truncate">Kabupaten Bengkulu Tengah</span>
                </span>
            </a>

            @php
                $navQuery = $publicFilterQuery ?? [];
                $navItems = [
                    ['route' => 'home', 'label' => 'Beranda', 'short' => 'Beranda', 'query' => $navQuery, 'path' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
                    ['route' => 'public.markets', 'label' => 'Pasar Terdekat', 'short' => 'Pasar', 'query' => $navQuery, 'path' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
                    ['route' => 'public.trends', 'label' => 'Tren & Insight', 'short' => 'Tren', 'query' => $navQuery, 'path' => 'M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941'],
                    ['route' => 'public.table', 'label' => 'Tabel', 'short' => 'Tabel', 'query' => $navQuery, 'path' => 'M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h17.25c.621 0 1.125-.504 1.125-1.125M2.25 18.375V5.625m0 0c0-.621.504-1.125 1.125-1.125h17.25c.621 0 1.125.504 1.125 1.125M2.25 5.625v12.75m19.5-12.75v12.75M2.25 9.75h19.5M2.25 14.25h19.5M9 4.5v15'],
                    ['route' => 'public.report', 'label' => 'Lapor Harga', 'short' => 'Lapor', 'query' => [], 'path' => 'M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z'],
                ];
                $mobileNavRoutes = ['home', 'public.markets', 'public.trends', 'public.table', 'public.report'];
            @endphp
            <nav class="hidden lg:flex items-center gap-1 text-sm font-medium text-slate-600" aria-label="Navigasi informasi">
                @foreach($navItems as $navItem)
                    <a href="{{ route($navItem['route'], $navItem['query']) }}"
                       @if(request()->routeIs($navItem['route'])) aria-current="page" @endif
                       class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs($navItem['route']) ? 'bg-brand-50 text-brand-800 font-semibold' : 'hover:bg-slate-100 hover:text-ink' }}">{{ $navItem['label'] }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-medium text-amber-800 bg-amber-50 ring-1 ring-amber-200 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Data simulasi
                </span>
                <a href="{{ route('public.report') }}" class="lg:hidden text-sm font-semibold text-brand-700 px-2 py-2">Lapor</a>
                <a href="{{ auth()->check() ? auth()->user()->homeRoute() : route('login') }}" class="bg-ink hover:bg-slate-800 text-white font-semibold text-sm px-4 py-2 rounded-lg transition-colors">
                    {{ auth()->check() ? 'Ruang kerja' : 'Masuk' }}
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1">
        @if(session('success'))
            <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-4">
                <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-brand-50 ring-1 ring-brand-200 text-brand-900 text-sm font-medium">
                    <svg class="w-5 h-5 shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-4">
                <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-rose-50 ring-1 ring-rose-200 text-rose-900 text-sm font-medium">
                    <svg class="w-5 h-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 pb-20 lg:pb-0">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-5 flex flex-col md:flex-row md:items-center gap-3 md:gap-6 text-xs text-slate-500">
            <div class="flex items-center gap-2.5 shrink-0">
                <img src="{{ asset('images/lambang-bengkulu-tengah.jpg') }}" alt="" class="h-8 w-auto" width="24" height="32">
                <div class="leading-tight">
                    <p class="font-display font-extrabold text-ink uppercase tracking-wide text-[13px]">Pantau Harga</p>
                    <p>&copy; {{ now()->year }} Pemkab Bengkulu Tengah</p>
                </div>
            </div>
            <p class="md:flex-1 leading-relaxed">
                Sumber: laporan harga terverifikasi petugas pantau.
                @if(!empty($lastUpdated))
                    Pemutakhiran <span class="font-semibold text-ink">{{ $lastUpdated->translatedFormat('d M Y H:i') }}</span>.
                @else
                    Belum ada data terverifikasi.
                @endif
            </p>
            <a href="{{ route('login') }}" class="shrink-0 font-semibold text-brand-700 hover:text-brand-800">Masuk petugas &amp; mitra &rarr;</a>
        </div>
    </footer>

    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-t border-slate-200 safe-bottom" aria-label="Navigasi informasi ponsel">
        <div class="grid grid-cols-5 max-w-lg mx-auto">
            @foreach(collect($navItems)->whereIn('route', $mobileNavRoutes) as $navItem)
                @php $isActiveNav = request()->routeIs($navItem['route']); @endphp
                <a href="{{ route($navItem['route'], $navItem['query']) }}" @if($isActiveNav) aria-current="page" @endif
                   class="flex flex-col items-center gap-0.5 py-2 text-[10px] font-semibold {{ $isActiveNav ? 'text-brand-700' : 'text-slate-500 hover:text-brand-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActiveNav ? '2.2' : '1.8' }}" d="{{ $navItem['path'] }}"/></svg>
                    {{ $navItem['short'] }}
                </a>
            @endforeach
        </div>
    </nav>

    <script>
        document.getElementById('filterToggle')?.addEventListener('click', function () {
            const panel = document.getElementById('filterPanel');
            const isOpen = ! panel.classList.toggle('hidden');
            this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            this.querySelector('[data-chevron]').classList.toggle('rotate-180', isOpen);
        });

        document.querySelectorAll('[data-use-location]').forEach(function (button) {
            button.addEventListener('click', function () {
                const label = button.querySelector('[data-label]');
                if (! navigator.geolocation) {
                    label.textContent = 'Lokasi tidak didukung';
                    return;
                }
                button.disabled = true;
                label.textContent = 'Mencari lokasi…';
                navigator.geolocation.getCurrentPosition(function (position) {
                    const url = new URL(button.dataset.locationUrl || window.location.href);
                    url.searchParams.set('lat', position.coords.latitude.toFixed(6));
                    url.searchParams.set('lng', position.coords.longitude.toFixed(6));
                    window.location.href = url.toString();
                }, function () {
                    button.disabled = false;
                    label.textContent = 'Lokasi tidak tersedia';
                });
            });
        });
    </script>
</body>
</html>
