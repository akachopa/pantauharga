<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title', 'PANTAU HARGA - Sistem Informasi Pemantauan Harga & Stok Bengkulu Tengah')</title>
    @stack('head')
    <script src="https://cdn.tailwindcss.com/3.4.17?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
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
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F4F7F6;
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
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.18s ease-out forwards;
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
        .form-embedded,
        .form-embedded:focus,
        .form-embedded:focus-visible {
            appearance: none;
            background-color: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .form-field,
        .form-field:focus,
        .form-field:focus-visible {
            appearance: none;
            box-shadow: none !important;
            outline: none !important;
            --tw-ring-inset: initial;
            --tw-ring-offset-width: 0px;
            --tw-ring-color: transparent;
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
        }
        select.form-field {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394A3B8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.7rem center;
            background-size: 14px 14px;
            padding-right: 2rem;
        }
        input[type="date"].form-field::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.55;
        }
        .select2-container {
            width: 100% !important;
            font-family: Inter, sans-serif;
        }
        .select2-container--default .select2-selection--single {
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            min-height: 38px;
            height: auto;
            padding: 4px 8px;
            background-color: #fff;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1E293B;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.4;
            padding-left: 2px;
            padding-right: 20px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            top: 0;
            right: 6px;
        }
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #0D9462;
            outline: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94A3B8;
        }
        .select2-dropdown {
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            overflow: hidden;
            z-index: 80;
            font-family: Inter, sans-serif;
        }
        .select2-container--default .select2-search--dropdown {
            padding: 8px;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #E2E8F0 !important;
            border-radius: 0.75rem !important;
            padding: 8px 12px !important;
            font-size: 12px !important;
            outline: none !important;
            box-shadow: none !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #0D9462 !important;
        }
        .select2-container--default .select2-results__option {
            font-size: 12px;
            padding: 8px 12px;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #0D9462;
        }
        .select2-container.select2-plain .select2-selection--single {
            border: 0;
            background: transparent;
            min-height: 0;
            padding: 2px 0 0;
            border-radius: 0;
        }
        .select2-container.select2-plain .select2-selection--single .select2-selection__rendered {
            padding-left: 0;
        }
        .select2-container.select2-plain .select2-selection--single .select2-selection__arrow {
            right: 0;
        }
        .select2-container.select2-plain.select2-container--open .select2-selection--single,
        .select2-container.select2-plain.select2-container--focus .select2-selection--single {
            border-color: transparent;
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
    </style>
</head>
<body class="min-h-screen flex flex-col lg:flex-row overflow-x-hidden">

    <div id="mobileDrawerOverlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden transition-opacity"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between transform -translate-x-full lg:translate-x-0 lg:static lg:shrink-0 transition-transform duration-200 ease-in-out">
        <div>
            <div class="px-5 py-5 flex items-center justify-between border-b border-slate-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 shrink-0 border border-brand-100">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.5-1.5-7.5-5.5-7.5-10.5C4.5 5.5 9 3 15 3c0 6 2.5 9.5-3 18z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c0-6 2.5-9.5 3-13"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-sm tracking-tight leading-tight">PANTAU HARGA</div>
                        <div class="text-xs text-slate-500 font-medium">Bengkulu Tengah</div>
                    </div>
                </a>
                <button id="closeMobileMenuBtn" type="button" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100" aria-label="Tutup menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @php
                $navActive = 'nav-item w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-brand-50 text-brand-700 border border-brand-200/60 transition-colors';
                $navIdle = 'nav-item w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 transition-colors';
            @endphp

            <nav class="p-3.5 space-y-1" aria-label="Menu Utama">
                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? $navActive : $navIdle }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5.5v-6h-5v6H4a1 1 0 01-1-1v-9.5z"/></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('prices.index') }}"
                   class="{{ request()->routeIs('prices.*') ? $navActive : $navIdle }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-5m3 5v-8m3 8v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Input Harga dan Tabulasi</span>
                </a>

                <a href="{{ route('master.index') }}"
                   class="{{ request()->routeIs('master.*') ? $navActive : $navIdle }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    <span>Master Data</span>
                </a>

                <a href="{{ route('radar.index') }}"
                   class="{{ request()->routeIs('radar.*') ? $navActive : $navIdle }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span>Radar</span>
                </a>

                <a href="{{ route('supply.index') }}"
                   class="{{ request()->routeIs('supply.*') ? $navActive : $navIdle }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>Rantai Pasok</span>
                </a>

                <a href="{{ route('policies.index') }}"
                   class="{{ request()->routeIs('policies.*') ? $navActive : $navIdle }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Paket Kebijakan</span>
                </a>

                <a href="{{ route('actions.index') }}"
                   class="{{ request()->routeIs('actions.*') ? $navActive : $navIdle }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="flex-1 flex items-center justify-between">
                        <span>Tindak Lanjut</span>
                        @if(($openActionCount ?? 0) > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">{{ $openActionCount }}</span>
                        @endif
                    </div>
                </a>

                <a href="{{ route('reports.index') }}"
                   class="{{ request()->routeIs('reports.*') ? $navActive : $navIdle }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan</span>
                </a>

                @if($currentUser?->hasRole('admin'))
                    <a href="{{ route('settings.index') }}"
                       class="{{ request()->routeIs('settings.*') ? $navActive : $navIdle }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Pengaturan</span>
                    </a>
                @endif
            </nav>
        </div>

        <div class="p-4 border-t border-slate-100 text-xs text-slate-500 flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-brand-600 text-white font-semibold text-xs flex items-center justify-center shrink-0">
                {{ substr($currentUser->name ?? 'A', 0, 1) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-semibold text-slate-800 truncate">{{ $currentUser->name ?? 'Administrator' }}</div>
                <div class="text-[11px] text-slate-400 truncate">{{ $currentUser ? $currentUser->roleName() : 'Admin Bappeda & Ketahanan Pangan' }}</div>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 pb-20 lg:pb-8">

        <header class="bg-white/90 backdrop-blur border-b border-slate-200/70 sticky top-0 z-30 px-4 lg:px-8 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <button id="openMobileMenuBtn" type="button" class="lg:hidden p-2 -ml-1 rounded-xl text-slate-700 hover:bg-slate-100" aria-label="Buka menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="flex lg:hidden items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 border border-brand-100">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.5-1.5-7.5-5.5-7.5-10.5C4.5 5.5 9 3 15 3c0 6 2.5 9.5-3 18z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-xs leading-tight">PANTAU HARGA</div>
                        <div class="text-[11px] text-slate-500 leading-tight">Bengkulu Tengah</div>
                    </div>
                </div>

                <nav class="hidden lg:flex items-center gap-2 text-xs text-slate-500" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-brand-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5.5v-6h-5v6H4a1 1 0 01-1-1v-9.5z"/></svg>
                        <span>Beranda</span>
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">@yield('breadcrumb', 'Dashboard')</span>
                </nav>
            </div>

            <div class="flex items-center gap-2.5 lg:gap-4">
                <div class="hidden md:flex items-center bg-slate-100/90 border border-slate-200/70 rounded-full px-3.5 py-1.5 w-64 lg:w-72">
                    <svg class="w-4 h-4 text-slate-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" placeholder="Cari komoditi, lokasi, atau laporan..." class="form-plain text-xs text-slate-700 placeholder-slate-400" />
                </div>

                <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide bg-lime-100 text-emerald-800 border border-lime-300/80">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    DATA SIMULASI
                </span>

                <div class="hidden sm:flex items-center gap-1.5 text-xs text-slate-600 font-medium">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ now()->translatedFormat('d M Y') }}</span>
                </div>

                <div class="relative">
                    <button id="notifBtn" type="button" onclick="document.getElementById('notif-menu').classList.toggle('hidden')" class="relative p-2 rounded-full text-slate-600 hover:bg-slate-100 transition-colors" aria-label="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
                    </button>
                    <div id="notif-menu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 p-3 z-50 animate-fade-in">
                        @php $headerAlerts = $headerAlerts ?? collect(); @endphp
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-xs font-bold text-slate-800">Notifikasi Peringatan</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $headerAlerts->isEmpty() ? 'bg-slate-100 text-slate-600' : 'bg-rose-100 text-rose-700' }}">{{ $headerAlerts->count() }} item</span>
                        </div>
                        <div class="py-2 space-y-2 text-xs max-h-72 overflow-y-auto">
                            @forelse($headerAlerts as $alert)
                                <a href="{{ $alert['url'] }}" class="block p-2 rounded-xl hover:bg-slate-50 transition">
                                    <div class="font-bold {{ ($alert['tone'] ?? 'amber') === 'rose' ? 'text-rose-600' : 'text-amber-600' }} flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full {{ ($alert['tone'] ?? 'amber') === 'rose' ? 'bg-rose-500' : 'bg-amber-500' }}"></span>
                                        {{ $alert['title'] }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $alert['detail'] }}</div>
                                </a>
                            @empty
                                <div class="p-2 text-slate-500">Tidak ada peringatan aktif saat ini.</div>
                            @endforelse
                        </div>
                        <div class="pt-2 border-t border-slate-100 text-center">
                            <a href="{{ route('actions.index') }}" class="text-[11px] font-bold text-brand-700 hover:text-brand-800">Lihat Semua Tindak Lanjut &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="relative" id="profile-container">
                    <button id="profileMenuBtn" type="button" onclick="document.getElementById('user-menu').classList.toggle('hidden')" class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-full hover:bg-slate-100 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-brand-600 text-white font-semibold text-xs flex items-center justify-center">
                            {{ substr($currentUser->name ?? 'A', 0, 1) }}
                        </div>
                        <span class="hidden sm:inline text-xs font-semibold text-slate-700">{{ $currentUser->name ?? 'Admin' }}</span>
                        <svg class="hidden sm:inline w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-50 animate-fade-in">
                        <div class="px-3 py-2 border-b border-slate-100">
                            <div class="text-xs font-bold text-slate-800">{{ $currentUser->name ?? 'Admin' }}</div>
                            <div class="text-[11px] text-brand-700 font-semibold">{{ $currentUser ? $currentUser->roleName() : 'Administrator' }}</div>
                            @if($allUsers->isNotEmpty())
                                <div class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-wider">Beralih Peran (Mode Demo)</div>
                            @endif
                        </div>
                        <div class="py-1 max-h-60 overflow-y-auto space-y-1 custom-scrollbar">
                            @foreach($allUsers as $u)
                                <a href="{{ route('switch.user', $u->id) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium hover:bg-brand-50 transition {{ ($currentUser && $currentUser->id == $u->id) ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700' }}">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-700 shrink-0">
                                            {{ substr($u->name, 0, 1) }}
                                        </div>
                                        <div class="truncate">
                                            <div class="truncate text-xs">{{ $u->name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $u->roleName() }}</div>
                                        </div>
                                    </div>
                                    @if($currentUser && $currentUser->id == $u->id)
                                        <span class="w-2 h-2 rounded-full bg-brand-600 shrink-0"></span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                        <div class="border-t border-slate-100 pt-1 mt-1">
                            @if($currentUser?->hasRole('admin'))
                                <a href="{{ route('settings.index', ['tab' => 'pengguna']) }}" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-brand-700 hover:bg-slate-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                    <span>Kelola Semua Pengguna</span>
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="sm:hidden bg-white px-4 py-2 border-b border-slate-100 flex items-center justify-between">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide bg-lime-100 text-emerald-800 border border-lime-300">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                DATA SIMULASI
            </span>
            <div class="flex items-center gap-1.5 text-xs text-slate-600 font-medium">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>{{ now()->translatedFormat('d M Y') }}</span>
            </div>
        </div>

        @if(session('success'))
            <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-900 text-xs font-semibold animate-fade-in">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between text-rose-900 text-xs font-semibold animate-fade-in">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold animate-fade-in">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <main id="appMain" class="flex-1 px-4 sm:px-6 lg:px-8 py-5 w-full space-y-6">
            @yield('content')
        </main>

    </div>

    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 z-30 grid grid-cols-4 py-1.5 px-2 shadow-lg" aria-label="Navigasi Bawah Mobile">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center py-1 rounded-lg {{ request()->routeIs('dashboard') ? 'text-[11px] font-semibold text-brand-600' : 'text-[11px] font-medium text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5.5v-6h-5v6H4a1 1 0 01-1-1v-9.5z"/></svg>
            <span>Beranda</span>
        </a>
        <a href="{{ route('prices.index') }}" class="flex flex-col items-center justify-center py-1 rounded-lg {{ request()->routeIs('prices.*') ? 'text-[11px] font-semibold text-brand-600' : 'text-[11px] font-medium text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
            <span>Data</span>
        </a>
        <a href="{{ route('actions.index') }}" class="flex flex-col items-center justify-center py-1 rounded-lg {{ request()->routeIs('actions.*') ? 'text-[11px] font-semibold text-brand-600' : 'text-[11px] font-medium text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Tugas</span>
        </a>
        <button type="button" id="mobileBottomMenuBtn" class="flex flex-col items-center justify-center py-1 rounded-lg text-[11px] font-medium text-slate-500 hover:text-brand-600">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span>Menu</span>
        </button>
    </nav>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileDrawerOverlay');
        const openBtn = document.getElementById('openMobileMenuBtn');
        const closeBtn = document.getElementById('closeMobileMenuBtn');
        const bottomMenuBtn = document.getElementById('mobileBottomMenuBtn');

        function openSidebar() {
            if (sidebar) sidebar.classList.remove('-translate-x-full');
            if (overlay) overlay.classList.remove('hidden');
        }

        function closeSidebar() {
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
        }

        if (openBtn) openBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (bottomMenuBtn) bottomMenuBtn.addEventListener('click', openSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);

        document.addEventListener('click', function(e) {
            const userMenu = document.getElementById('user-menu');
            const profileBtn = document.getElementById('profileMenuBtn');
            if (userMenu && profileBtn && !profileBtn.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
            }

            const notifMenu = document.getElementById('notif-menu');
            const notifBtn = document.getElementById('notifBtn');
            if (notifMenu && notifBtn && !notifBtn.contains(e.target) && !notifMenu.contains(e.target)) {
                notifMenu.classList.add('hidden');
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function initSelect2(context) {
            const $root = context ? $(context) : $(document);
            $root.find('select').each(function () {
                const $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }

                const $modal = $el.closest('[id^="modal-"]');
                const isPlain = $el.hasClass('form-plain');
                const emptyOption = $el.find('option[value=""]').first();

                $el.select2({
                    width: '100%',
                    minimumResultsForSearch: 0,
                    dropdownParent: $modal.length ? $modal : $(document.body),
                    placeholder: emptyOption.length ? emptyOption.text() : undefined,
                    language: {
                        noResults: function () { return 'Tidak ada hasil'; },
                        searching: function () { return 'Mencari...'; }
                    }
                });

                if (isPlain) {
                    $el.next('.select2-container').addClass('select2-plain');
                }
            });
        }

        $(function () {
            initSelect2();

            document.querySelectorAll('[id^="modal-"]').forEach(function (modal) {
                new MutationObserver(function () {
                    if (!modal.classList.contains('hidden')) {
                        initSelect2(modal);
                    }
                }).observe(modal, { attributes: true, attributeFilter: ['class'] });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
