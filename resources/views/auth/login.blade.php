@extends('layouts.portal')

@section('title', 'Masuk - PANTAU HARGA Bengkulu Tengah')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 sm:py-16 grid lg:grid-cols-2 gap-10 items-center">
        <div class="hidden lg:block">
            <p class="text-sm font-semibold text-brand-700">Ruang kerja Pantau Harga</p>
            <h1 class="mt-2 text-4xl font-black tracking-tight text-ink leading-tight">Satu pintu masuk untuk petugas dinas dan mitra usaha.</h1>
            <dl class="mt-8 space-y-4 text-sm">
                <div class="flex gap-3">
                    <span class="mt-0.5 w-8 h-8 shrink-0 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center font-bold">1</span>
                    <div>
                        <dt class="font-semibold text-ink">Tim operator input</dt>
                        <dd class="text-slate-500">Harga yang diinput langsung terverifikasi dan tampil di informasi publik.</dd>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="mt-0.5 w-8 h-8 shrink-0 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold">2</span>
                    <div>
                        <dt class="font-semibold text-ink">Mitra usaha</dt>
                        <dd class="text-slate-500">Kirim harga dari lokasi usaha Anda. Data tampil setelah diverifikasi petugas dinas.</dd>
                    </div>
                </div>
            </dl>
        </div>

        <div class="w-full max-w-md mx-auto bg-white rounded-2xl ring-1 ring-slate-200/70 p-6 sm:p-8 shadow-sm">
            <h2 class="text-2xl font-extrabold tracking-tight text-ink">Masuk</h2>
            <p class="text-sm text-slate-500 mt-1">Gunakan akun yang diberikan oleh admin dinas.</p>

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="mt-1 w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm @error('email') border-rose-400 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Kata sandi</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="mt-1 w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm @error('password') border-rose-400 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    Ingat saya di perangkat ini
                </label>
                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm py-2.5 rounded-lg transition-colors">Masuk</button>
            </form>
            <p class="mt-5 text-xs text-slate-400">Belum punya akun mitra? Hubungi Disperindag Kab. Bengkulu Tengah untuk didaftarkan.</p>
        </div>
    </div>
@endsection
