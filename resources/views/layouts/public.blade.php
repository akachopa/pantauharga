<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport">
    <meta name="theme-color" content="#065F46">
    <title>@yield('title', 'Informasi Harga Pangan - PANTAU HARGA Bengkulu Tengah')</title>
    <script src="https://cdn.tailwindcss.com/3.4.17?plugins=forms,container-queries"></script>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/lambang-bengkulu-tengah.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ECFDF5',
                            100: '#D1FAE5',
                            200: '#A7F3D0',
                            500: '#10B981',
                            600: '#0D9462',
                            700: '#047857',
                            800: '#065F46',
                            900: '#064E3B',
                        }
                    },
                    boxShadow: {
                        'card': '0 1px 3px rgba(15, 23, 42, 0.04), 0 4px 12px rgba(15, 23, 42, 0.03)',
                        'card-hover': '0 4px 6px -1px rgba(15, 23, 42, 0.06), 0 10px 24px -3px rgba(15, 23, 42, 0.05)',
                    }
                }
            }
        }
    </script>
    <style>
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F5F5F2;
            color: #1E293B;
            -webkit-tap-highlight-color: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        .card-shadow,
        .shadow-card {
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 4px 12px rgba(15, 23, 42, 0.03);
        }
        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }
        .form-plain,
        .form-plain:focus,
        .form-plain:focus-visible {
            appearance: none;
            background-color: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            outline: none !important;
            padding: 0 !important;
            margin: 0;
            min-height: 0;
            line-height: inherit;
            color: inherit;
            width: 100%;
        }
        select.form-plain {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394A3B8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0 center;
            background-size: 14px 14px;
            padding-right: 18px !important;
        }
        input[type="date"].form-plain::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.55;
        }
        table {
            font-family: Inter, sans-serif;
            letter-spacing: 0;
            border-collapse: collapse;
        }
        table thead th {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748B;
            letter-spacing: 0 !important;
            text-transform: none !important;
        }
        table tbody {
            font-size: 0.875rem;
        }
        table tbody td {
            letter-spacing: 0;
        }
        .no-scrollbar {
            scrollbar-width: none;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <header class="bg-white border-b border-slate-300 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 sm:h-16 flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 min-w-0">
                <img src="{{ asset('images/lambang-bengkulu-tengah.jpg') }}" alt="Lambang Kabupaten Bengkulu Tengah" class="h-10 sm:h-11 w-auto shrink-0" width="33" height="43">
                <div class="min-w-0">
                    <div class="font-bold text-slate-900 text-sm tracking-tight leading-tight">PANTAU HARGA</div>
                    <div class="text-[11px] sm:text-xs text-slate-500 font-medium truncate">Kabupaten Bengkulu Tengah</div>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-1 text-sm font-semibold text-slate-600" aria-label="Navigasi informasi">
                <a href="#insight" class="px-3 py-2 hover:text-brand-800 hover:underline underline-offset-[6px] decoration-2">Insight</a>
                <a href="#harga-hari-ini" class="px-3 py-2 hover:text-brand-800 hover:underline underline-offset-[6px] decoration-2">Harga Hari Ini</a>
                <a href="#pasar-terdekat" class="px-3 py-2 hover:text-brand-800 hover:underline underline-offset-[6px] decoration-2">Pasar Terdekat</a>
                <a href="#tren" class="px-3 py-2 hover:text-brand-800 hover:underline underline-offset-[6px] decoration-2">Tren</a>
                <a href="#tabel" class="px-3 py-2 hover:text-brand-800 hover:underline underline-offset-[6px] decoration-2">Tabel</a>
                <a href="#lapor-harga" class="px-3 py-2 hover:text-brand-800 hover:underline underline-offset-[6px] decoration-2">Lapor Harga</a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <span class="hidden sm:inline text-[11px] font-semibold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">
                    Data simulasi
                </span>
                <a href="{{ route('dashboard') }}" class="border border-slate-300 hover:border-slate-500 text-slate-800 font-semibold text-xs sm:text-sm px-3 py-1.5 rounded transition-colors">
                    Masuk
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <div class="flex items-start gap-2 p-3 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-semibold">
                    <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <div class="flex items-start gap-2 p-3 rounded-md bg-rose-50 border border-rose-200 text-rose-900 text-sm font-semibold">
                    <svg class="w-5 h-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="border-t border-slate-300 bg-white pb-20 lg:pb-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row md:justify-between gap-4 text-xs text-slate-600">
            <div class="flex items-start gap-3 max-w-xl">
                <img src="{{ asset('images/lambang-bengkulu-tengah.jpg') }}" alt="" class="h-12 w-auto shrink-0" width="36" height="48">
                <div class="space-y-1">
                <p class="font-semibold text-slate-800">Pantau Harga Kabupaten Bengkulu Tengah</p>
                <p>Sumber data: laporan harga yang telah diverifikasi oleh petugas pantau harga. Harga rata-rata dihitung dari titik pantau yang terverifikasi pada tanggal yang dipilih.</p>
                </div>
            </div>
            <div class="space-y-1 md:text-right">
                <p>
                    @if(!empty($lastUpdated))
                        Pemutakhiran terakhir: <span class="font-semibold text-slate-800">{{ $lastUpdated->translatedFormat('d M Y H:i') }}</span>
                    @else
                        Belum ada data terverifikasi.
                    @endif
                </p>
                <p>&copy; {{ now()->year }} Pemerintah Kabupaten Bengkulu Tengah</p>
                <a href="{{ route('dashboard') }}" class="font-semibold text-brand-800 underline underline-offset-2">Masuk ke ruang kerja</a>
            </div>
        </div>
    </footer>

    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-slate-300 safe-bottom" aria-label="Navigasi informasi ponsel">
        <div class="grid grid-cols-5 max-w-lg mx-auto">
            <a href="#harga-hari-ini" class="flex flex-col items-center gap-0.5 py-2 text-[10px] font-semibold text-slate-500 hover:text-brand-700 active:text-brand-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3zM6 6h.008v.008H6V6z"/></svg>
                Harga
            </a>
            <a href="#pasar-terdekat" class="flex flex-col items-center gap-0.5 py-2 text-[10px] font-semibold text-slate-500 hover:text-brand-700 active:text-brand-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                Pasar
            </a>
            <a href="#tren" class="flex flex-col items-center gap-0.5 py-2 text-[10px] font-semibold text-slate-500 hover:text-brand-700 active:text-brand-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                Tren
            </a>
            <a href="#tabel" class="flex flex-col items-center gap-0.5 py-2 text-[10px] font-semibold text-slate-500 hover:text-brand-700 active:text-brand-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5"/></svg>
                Tabel
            </a>
            <a href="#lapor-harga" class="flex flex-col items-center gap-0.5 py-2 text-[10px] font-semibold text-slate-500 hover:text-brand-700 active:text-brand-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                Lapor
            </a>
        </div>
    </nav>
</body>
</html>
