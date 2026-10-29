@extends('layouts.app')

@section('title', 'Pengaturan & Akses Pengguna - PANTAU HARGA')
@section('breadcrumb', 'Pengaturan')

@section('content')
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Pengaturan</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Akses yang tepat untuk setiap pengguna.</p>
    </div>

    @php
        $settingsTabs = [
            'pengguna' => ['label' => 'Pengguna & Akses', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            'akses' => ['label' => 'Instansi', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            'notifikasi' => ['label' => 'Notifikasi', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
            'audit' => ['label' => 'Audit', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ];
    @endphp
    <div class="border-b border-slate-200 flex items-center gap-6 overflow-x-auto custom-scrollbar">
        @foreach($settingsTabs as $tabKey => $tab)
            <a href="{{ route('settings.index', ['tab' => $tabKey, 'user' => $selectedSettingsUser->id ?? null]) }}"
               class="pb-3 flex items-center gap-2 text-xs sm:text-sm font-semibold whitespace-nowrap border-b-2 transition-colors {{ $activeTab === $tabKey ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $tab['icon'] }}"/></svg>
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>

    @if($activeTab === 'pengguna' && $selectedSettingsUser)
        @php
            $roleName = strtolower($selectedSettingsUser->roleName());
            $roleKey = 'operator';
            foreach (['admin', 'verifikator', 'pimpinan', 'mitra', 'operator'] as $candidate) {
                if (str_contains($roleName, $candidate)) {
                    $roleKey = $candidate;
                    break;
                }
            }
            $matrix = [
                'admin' => [
                    'dashboard' => ['l' => true, 'i' => true, 'v' => true, 'e' => true],
                    'input' => ['l' => true, 'i' => true, 'v' => true, 'e' => true],
                    'master' => ['l' => true, 'i' => true, 'v' => true, 'e' => true],
                    'laporan' => ['l' => true, 'i' => true, 'v' => true, 'e' => true],
                ],
                'pimpinan' => [
                    'dashboard' => ['l' => true, 'i' => false, 'v' => false, 'e' => true],
                    'input' => ['l' => true, 'i' => false, 'v' => false, 'e' => true],
                    'master' => ['l' => true, 'i' => false, 'v' => false, 'e' => false],
                    'laporan' => ['l' => true, 'i' => false, 'v' => true, 'e' => true],
                ],
                'verifikator' => [
                    'dashboard' => ['l' => true, 'i' => false, 'v' => true, 'e' => false],
                    'input' => ['l' => true, 'i' => false, 'v' => true, 'e' => false],
                    'master' => ['l' => true, 'i' => false, 'v' => true, 'e' => false],
                    'laporan' => ['l' => true, 'i' => false, 'v' => true, 'e' => true],
                ],
                'operator' => [
                    'dashboard' => ['l' => true, 'i' => false, 'v' => false, 'e' => false],
                    'input' => ['l' => true, 'i' => true, 'v' => false, 'e' => false],
                    'master' => ['l' => true, 'i' => false, 'v' => false, 'e' => false],
                    'laporan' => ['l' => true, 'i' => false, 'v' => false, 'e' => true],
                ],
                'mitra' => [
                    'dashboard' => ['l' => true, 'i' => false, 'v' => false, 'e' => false],
                    'input' => ['l' => true, 'i' => true, 'v' => false, 'e' => false],
                    'master' => ['l' => false, 'i' => false, 'v' => false, 'e' => false],
                    'laporan' => ['l' => true, 'i' => false, 'v' => false, 'e' => false],
                ],
            ];
            $perms = $matrix[$roleKey];
            $menuRows = [
                ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5.5v-6h-5v6H4a1 1 0 01-1-1v-9.5z'],
                ['key' => 'input', 'label' => 'Input Harga', 'icon' => 'M9 17v-5m3 5v-8m3 8v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z'],
                ['key' => 'master', 'label' => 'Master Data', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
                ['key' => 'laporan', 'label' => 'Laporan', 'icon' => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z'],
            ];
            $inputTags = $markets->take($roleKey === 'mitra' ? 1 : 2)->pluck('name');
            $lihatScope = $roleKey === 'mitra' ? ($regions->first()->name ?? 'Kecamatan Karang Tinggi') : 'Seluruh kabupaten';
        @endphp

        <div class="lg:hidden bg-white rounded-2xl p-3.5 border border-slate-200/80 card-shadow flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-emerald-100 text-brand-700 font-bold text-sm flex items-center justify-center shrink-0">{{ substr($selectedSettingsUser->name, 0, 1) }}</div>
                <div class="min-w-0 flex-1">
                    <select id="mobileUserSelect" class="form-plain font-bold text-slate-900 text-sm" onchange="window.location=this.value">
                        @foreach($users as $user)
                            <option value="{{ route('settings.index', ['tab' => 'pengguna', 'user' => $user->id]) }}" @selected($selectedSettingsUser->id === $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <div class="text-xs text-slate-500 truncate">{{ $selectedSettingsUser->email }}</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="hidden lg:flex lg:col-span-4 bg-white rounded-2xl p-5 border border-slate-200/70 card-shadow flex-col justify-between gap-4">
                <div class="space-y-3.5">
                    <h3 class="text-base font-bold text-slate-900">Daftar Pengguna</h3>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 flex items-center">
                        <svg class="w-4 h-4 text-slate-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input id="userSearchInput" type="text" placeholder="Cari pengguna..." class="form-plain w-full text-xs text-slate-700">
                    </div>
                    <div class="space-y-1.5 pt-1">
                        @foreach($users as $user)
                            @php $isSel = $selectedSettingsUser->id === $user->id; @endphp
                            <a href="{{ route('settings.index', ['tab' => 'pengguna', 'user' => $user->id]) }}"
                               data-search="{{ strtolower($user->name.' '.$user->email) }}"
                               class="user-row-btn w-full p-3 rounded-xl flex items-center justify-between text-left transition-colors {{ $isSel ? 'bg-emerald-50/90 border border-emerald-200' : 'hover:bg-slate-50 border border-transparent' }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-full {{ $isSel ? 'bg-emerald-200 text-emerald-900' : 'bg-slate-100 text-slate-700' }} font-bold text-sm flex items-center justify-center shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 text-sm truncate">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-500 truncate">{{ $user->email }}</div>
                                    </div>
                                </div>
                                @if($isSel)
                                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
                <button type="button" onclick="openAddUserModal()" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm py-2.5 rounded-xl flex items-center justify-center gap-2 shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                    <span>Pengguna</span>
                </button>
            </div>

            <div class="lg:col-span-8 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/70 card-shadow space-y-5">
                <form action="{{ route('settings.users.update', $selectedSettingsUser->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $selectedSettingsUser->name }}">
                    <input type="hidden" name="email" value="{{ $selectedSettingsUser->email }}">
                    <input type="hidden" name="organization_id" value="{{ $selectedSettingsUser->organization_id }}">
                    @if($selectedSettingsUser->is_active)
                        <input type="hidden" name="is_active" value="1">
                    @endif

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Hak akses — {{ $selectedSettingsUser->name }}</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Atur menu dan aksi yang dapat diakses oleh pengguna ini.</p>
                        </div>
                        <div class="sm:w-48">
                            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Template peran</label>
                            <select name="role_id" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 bg-white">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" @selected($selectedSettingsUser->roles->contains('id', $role->id))>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="border border-slate-200/80 rounded-2xl overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/90 border-b border-slate-200/70 text-xs font-semibold text-slate-600">
                                    <th class="py-3 px-4">Menu</th>
                                    <th class="py-3 px-3 text-center"><span class="hidden sm:inline">Lihat</span><span class="sm:hidden">L</span></th>
                                    <th class="py-3 px-3 text-center"><span class="hidden sm:inline">Input</span><span class="sm:hidden">I</span></th>
                                    <th class="py-3 px-3 text-center"><span class="hidden sm:inline">Verifikasi</span><span class="sm:hidden">V</span></th>
                                    <th class="py-3 px-3 text-center"><span class="hidden sm:inline">Ekspor</span><span class="sm:hidden">E</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                                @foreach($menuRows as $row)
                                    <tr>
                                        <td class="py-3.5 px-4 font-semibold text-slate-800">
                                            <div class="flex items-center gap-2.5">
                                                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $row['icon'] }}"/></svg>
                                                <span>{{ $row['label'] }}</span>
                                            </div>
                                        </td>
                                        @foreach(['l', 'i', 'v', 'e'] as $act)
                                            <td class="py-3.5 px-3 text-center">
                                                <span class="w-5 h-5 rounded-md inline-flex items-center justify-center border {{ $perms[$row['key']][$act] ? 'bg-brand-600 border-brand-600 text-white' : 'bg-white border-slate-300 text-transparent' }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-slate-900">Cakupan Akses</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <div>
                                <label class="block text-xs text-slate-500 font-medium mb-1">Cakupan lihat</label>
                                <select class="form-field w-full border border-slate-200 rounded-xl px-3 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white">
                                    <option @selected($lihatScope === 'Seluruh kabupaten')>Seluruh kabupaten</option>
                                    @foreach($regions as $region)
                                        <option @selected($lihatScope === $region->name)>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 font-medium mb-1">Cakupan input</label>
                                <div class="border border-slate-200 rounded-xl p-2 min-h-[42px] flex flex-wrap gap-1.5 items-center bg-white">
                                    @foreach($inputTags as $tag)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium">
                                            <span>{{ $tag }}</span>
                                            <button type="button" data-remove-chip class="text-slate-400 hover:text-rose-600">×</button>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 font-medium mb-1">Titik pantau (lokasi input)</label>
                                <input type="hidden" name="assign_points" value="1">
                                <div class="border border-slate-200 rounded-xl p-2 max-h-40 overflow-y-auto custom-scrollbar space-y-1 bg-white">
                                    @foreach($points as $point)
                                        <label class="flex items-start gap-2 px-1.5 py-1 rounded-lg hover:bg-slate-50 text-xs text-slate-700 cursor-pointer">
                                            <input type="checkbox" name="point_ids[]" value="{{ $point->id }}" @checked(in_array($point->id, $assignedPointIds, true)) class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                            <span>{{ $point->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="mt-1 text-[11px] text-slate-400">Mitra dan operator hanya bisa menginput harga untuk titik yang dicentang.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                        <div class="bg-blue-50/80 border border-blue-200/70 rounded-xl px-3.5 py-2.5 flex items-center gap-2 text-xs text-blue-900 font-medium">
                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tidak dapat memverifikasi laporan sendiri.</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('switch.user', $selectedSettingsUser->id) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">Beralih</a>
                            @if($selectedSettingsUser->id !== 1)
                                <button type="submit" form="delete-user-{{ $selectedSettingsUser->id }}" class="px-4 py-2.5 rounded-xl border border-rose-200 text-sm font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                            @endif
                            <button type="submit" class="w-full sm:w-auto bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm px-6 py-2.5 rounded-xl shadow-xs transition-colors">Simpan perubahan</button>
                        </div>
                    </div>
                </form>
                @if($selectedSettingsUser->id !== 1)
                    <form id="delete-user-{{ $selectedSettingsUser->id }}" action="{{ route('settings.users.destroy', $selectedSettingsUser->id) }}" method="POST" onsubmit="return confirm('Hapus akun pengguna ini?')" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            </div>
        </div>

        <button type="button" onclick="openAddUserModal()" class="lg:hidden w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm py-2.5 rounded-xl flex items-center justify-center gap-2 shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
            <span>Pengguna</span>
        </button>
    @endif

    @if($activeTab === 'akses')
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/70 card-shadow space-y-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Instansi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Organisasi yang menaungi pengguna dan cakupan data.</p>
            </div>
            <div class="border border-slate-200/80 rounded-2xl overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 border-b border-slate-200/70 text-xs font-semibold text-slate-600">
                            <th class="py-3 px-4">Instansi</th>
                            <th class="py-3 px-4">Kode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($organizations as $org)
                            <tr>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $org->name }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $org->code ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-6 text-center text-slate-400">Belum ada instansi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($activeTab === 'notifikasi')
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/70 card-shadow space-y-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Notifikasi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pilih jenis peringatan yang tampil di header (disimpan di sesi).</p>
            </div>
            <form method="POST" action="{{ route('settings.notifications.update') }}" class="space-y-3 text-sm">
                @csrf
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 cursor-pointer">
                    <input type="checkbox" name="stock_low" value="1" class="mt-1" @checked($notificationPrefs['stock_low'] ?? true)>
                    <span>
                        <div class="font-bold">Stok menipis</div>
                        <div class="text-xs text-slate-600 mt-0.5">Muncul saat stok komoditi di bawah ambang batas.</div>
                    </span>
                </label>
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 cursor-pointer">
                    <input type="checkbox" name="report_missing" value="1" class="mt-1" @checked($notificationPrefs['report_missing'] ?? true)>
                    <span>
                        <div class="font-bold">Laporan belum masuk</div>
                        <div class="text-xs text-slate-600 mt-0.5">Muncul saat titik pantau belum mengirim harga harian.</div>
                    </span>
                </label>
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-blue-50/80 border border-blue-200/70 text-blue-900 cursor-pointer">
                    <input type="checkbox" name="need_verify" value="1" class="mt-1" @checked($notificationPrefs['need_verify'] ?? true)>
                    <span>
                        <div class="font-bold">Perlu verifikasi</div>
                        <div class="text-xs text-slate-600 mt-0.5">Muncul saat ada laporan yang menunggu keputusan.</div>
                    </span>
                </label>
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-violet-50 border border-violet-200 text-violet-900 cursor-pointer">
                    <input type="checkbox" name="policy_trigger" value="1" class="mt-1" @checked($notificationPrefs['policy_trigger'] ?? true)>
                    <span>
                        <div class="font-bold">Pemicu kebijakan</div>
                        <div class="text-xs text-slate-600 mt-0.5">Muncul saat rule paket kebijakan terpicu.</div>
                    </span>
                </label>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold">Simpan preferensi</button>
            </form>
        </div>
    @endif

    <!-- TAB 3: AUDIT LOG -->
    @if($activeTab === 'audit')
        <div class="bg-white rounded-2xl border border-slate-200/70 card-shadow p-6 space-y-6">
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-800">Audit Log Operasional</h3>
                <div class="overflow-x-auto border border-slate-100 rounded-xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                                <th class="py-3 px-4">Waktu</th>
                                <th class="py-3 px-4">Aktor</th>
                                <th class="py-3 px-4">Entitas</th>
                                <th class="py-3 px-4">Aksi</th>
                                <th class="py-3 px-4">Rincian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($auditLogs as $log)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="py-3.5 px-4 font-mono text-slate-500 text-xs">{{ $log->created_at }}</td>
                                    <td class="py-3.5 px-4 text-slate-700">{{ $log->actor_name ?? ('#'.$log->actor_id) }}</td>
                                    <td class="py-3.5 px-4 text-slate-700">{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-emerald-800">{{ $log->action }}</td>
                                    <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500 max-w-xs truncate">{{ $log->new_values }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">Belum ada audit log.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-800">Catatan Perubahan Hak Akses</h3>
                <div class="overflow-x-auto border border-slate-100 rounded-xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                                <th class="py-3 px-4">Waktu</th>
                                <th class="py-3 px-4">Tipe Perubahan</th>
                                <th class="py-3 px-4">Alasan</th>
                                <th class="py-3 px-4">Rincian Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($accessLogs as $log)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="py-3.5 px-4 font-mono text-slate-500">{{ $log->created_at }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-emerald-800">{{ $log->change_type }}</td>
                                    <td class="py-3.5 px-4 text-slate-700">{{ $log->reason }}</td>
                                    <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">{{ $log->after_json }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400">Tidak ada log akses.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

<!-- Modal Tambah User -->
<div id="modal-add-user" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Tambah Akun Pengguna Baru</h3>
            <button type="button" onclick="closeAddUserModal()" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
        </div>
        <form action="{{ route('settings.users.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" placeholder="Nama petugas..." required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Email Resmi <span class="text-red-500">*</span></label>
                <input type="email" name="email" placeholder="user@pantauharga.go.id" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NIP (PNS/PPPK)</label>
                    <input type="text" name="nip" placeholder="199..." class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">No. Handphone</label>
                    <input type="text" name="phone" placeholder="08..." class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Peran / Role Pengguna <span class="text-red-500">*</span></label>
                <select name="role_id" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Instansi / Organisasi</label>
                <select name="organization_id" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-700 font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl font-semibold hover:bg-brand-700">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddUserModal() { document.getElementById('modal-add-user').classList.remove('hidden'); }
    function closeAddUserModal() { document.getElementById('modal-add-user').classList.add('hidden'); }

    const userSearch = document.getElementById('userSearchInput');
    if (userSearch) {
        userSearch.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.user-row-btn').forEach(function (row) {
                const haystack = row.getAttribute('data-search') || '';
                row.classList.toggle('hidden', query !== '' && !haystack.includes(query));
            });
        });
    }

    document.querySelectorAll('[data-remove-chip]').forEach(function (button) {
        button.addEventListener('click', function () {
            button.parentElement.remove();
        });
    });
</script>
@endsection
