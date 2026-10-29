<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.partials.public-head')
</head>
<body class="min-h-screen flex flex-col antialiased">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 min-w-0">
                <img src="{{ asset('images/lambang-bengkulu-tengah.jpg') }}" alt="Lambang Kabupaten Bengkulu Tengah" class="h-10 w-auto shrink-0" width="30" height="40">
                <span class="min-w-0 leading-tight">
                    <span class="block font-display font-extrabold text-ink text-base uppercase tracking-wide">Pantau Harga</span>
                    <span class="block text-xs text-slate-500 truncate">@yield('portal-label', 'Kabupaten Bengkulu Tengah')</span>
                </span>
            </a>

            @auth
                <div class="flex items-center gap-3">
                    <div class="hidden sm:block text-right leading-tight">
                        <p class="text-sm font-semibold text-ink">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->roleName() }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-semibold text-slate-600 hover:text-rose-600 px-3 py-2 rounded-lg ring-1 ring-slate-200 hover:ring-rose-200 transition-colors">Keluar</button>
                    </form>
                </div>
            @else
                <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-600 hover:text-ink">&larr; Info harga publik</a>
            @endauth
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
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 text-xs text-slate-500 flex flex-wrap justify-between gap-2">
            <span>&copy; {{ now()->year }} Pemkab Bengkulu Tengah</span>
            <a href="{{ route('home') }}" class="font-semibold text-brand-700 hover:text-brand-800">Lihat harga publik &rarr;</a>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
