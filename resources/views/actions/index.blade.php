@extends('layouts.app')

@section('title', 'Tindak Lanjut - PANTAU HARGA')
@section('breadcrumb', 'Tindak Lanjut')

@section('content')
    @php
        $viewMode = request('view', 'kanban');
        $mobileTab = request('mobile', 'dalam_proses');
        $mobileList = match ($mobileTab) {
            'ditugaskan' => $ditugaskanList,
            'selesai' => $selesaiList,
            default => $dalamProsesList,
        };
        $filterQuery = request()->except(['view', 'mobile', 'selected']);
    @endphp

    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Tindak Lanjut</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Setiap arahan punya penanggung jawab dan tenggat.</p>
        </div>
        <button type="button" onclick="openAddActionModal()" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-xs shrink-0 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
            <span>Penugasan</span>
        </button>
    </div>

    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/70 card-shadow flex items-center gap-3">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="text-lg sm:text-2xl font-bold text-slate-900 leading-tight">{{ $countAntrean + $countTerlambat }}</div>
                <div class="text-[11px] sm:text-xs text-slate-500">Ditugaskan</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/70 card-shadow flex items-center gap-3">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div>
                <div class="text-lg sm:text-2xl font-bold text-slate-900 leading-tight">{{ $countDalamProses }}</div>
                <div class="text-[11px] sm:text-xs text-slate-500">Dalam proses</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/70 card-shadow flex items-center gap-3">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-50 text-brand-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="text-lg sm:text-2xl font-bold text-slate-900 leading-tight">{{ $countSelesai }}</div>
                <div class="text-[11px] sm:text-xs text-slate-500">Selesai</div>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('actions.index') }}" class="hidden md:flex items-end justify-between gap-4 bg-white rounded-2xl p-4 border border-slate-200/70 card-shadow">
        <input type="hidden" name="view" value="{{ $viewMode }}">
        @if($selectedId)
            <input type="hidden" name="selected" value="{{ $selectedId }}">
        @endif
        <div class="grid grid-cols-3 gap-3 flex-1 max-w-2xl">
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">PIC</label>
                <select name="pic_id" onchange="this.form.submit()" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800">
                    <option value="">Semua PIC</option>
                    @foreach($officers as $off)
                        <option value="{{ $off->id }}" @selected($filterPic == $off->id)>{{ $off->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Wilayah</label>
                <select name="kecamatan_id" onchange="this.form.submit()" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800">
                    <option value="">Semua Wilayah</option>
                    @foreach($kecamatans as $kec)
                        <option value="{{ $kec->id }}" @selected(($filterRegion ?? null) == $kec->id)>{{ $kec->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Prioritas</label>
                <select name="priority" onchange="this.form.submit()" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800">
                    <option value="">Semua Prioritas</option>
                    <option value="tinggi" @selected($filterPriority === 'tinggi')>Tinggi</option>
                    <option value="sedang" @selected($filterPriority === 'sedang')>Sedang</option>
                    <option value="rendah" @selected($filterPriority === 'rendah')>Rendah</option>
                </select>
            </div>
        </div>
        <div class="inline-flex bg-slate-100 p-1 rounded-xl border border-slate-200/70">
            <a href="{{ route('actions.index', array_merge($filterQuery, ['view' => 'kanban', 'selected' => $selectedId])) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 {{ $viewMode !== 'list' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
                <span>Kanban</span>
            </a>
            <a href="{{ route('actions.index', array_merge($filterQuery, ['view' => 'list', 'selected' => $selectedId])) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 {{ $viewMode === 'list' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <span>Daftar</span>
            </a>
        </div>
    </form>

    @if($viewMode !== 'list')
        <div class="hidden md:grid grid-cols-3 gap-5 items-start">
            <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-4 space-y-3.5">
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-2 font-bold text-sm text-blue-950">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Ditugaskan</span>
                    </div>
                    <span class="text-xs font-bold text-blue-700">{{ $ditugaskanList->count() }}</span>
                </div>
                <div class="space-y-3">
                    @foreach($ditugaskanList as $act)
                        @include('actions.partials.task-card', ['act' => $act, 'selectedAction' => $selectedAction])
                    @endforeach
                </div>
            </div>
            <div class="bg-amber-50/60 border border-amber-100 rounded-2xl p-4 space-y-3.5">
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-2 font-bold text-sm text-amber-950">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Dalam proses</span>
                    </div>
                    <span class="text-xs font-bold text-amber-700">{{ $dalamProsesList->count() }}</span>
                </div>
                <div class="space-y-3">
                    @foreach($dalamProsesList as $act)
                        @include('actions.partials.task-card', ['act' => $act, 'selectedAction' => $selectedAction])
                    @endforeach
                </div>
            </div>
            <div class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-4 space-y-3.5">
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-2 font-bold text-sm text-emerald-950">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Selesai</span>
                    </div>
                    <span class="text-xs font-bold text-brand-700">{{ $selesaiList->count() }}</span>
                </div>
                <div class="space-y-3">
                    @foreach($selesaiList as $act)
                        @include('actions.partials.task-card', ['act' => $act, 'selectedAction' => $selectedAction])
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="hidden md:block space-y-3">
            @foreach($actions as $act)
                @include('actions.partials.task-card', ['act' => $act, 'selectedAction' => $selectedAction])
            @endforeach
        </div>
    @endif

    <div class="md:hidden space-y-4">
        <div class="border-b border-slate-200 grid grid-cols-3 text-center">
            @foreach(['ditugaskan' => 'Ditugaskan', 'dalam_proses' => 'Dalam proses', 'selesai' => 'Selesai'] as $tabKey => $tabLabel)
                <a href="{{ route('actions.index', array_merge($filterQuery, ['mobile' => $tabKey, 'view' => $viewMode, 'selected' => $selectedId])) }}"
                   class="pb-2.5 text-xs font-semibold border-b-2 {{ $mobileTab === $tabKey ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500' }}">
                    {{ $tabLabel }}
                </a>
            @endforeach
        </div>
        <div class="space-y-3">
            @foreach($mobileList as $act)
                @include('actions.partials.task-card', ['act' => $act, 'selectedAction' => $selectedAction])
            @endforeach
        </div>
    </div>

    @if($selectedAction)
        @php
            $selectedPriority = match ($selectedAction->priority) {
                'tinggi' => 'Tinggi',
                'sedang' => 'Sedang',
                default => 'Rendah',
            };
            $selectedPriorityClass = match ($selectedAction->priority) {
                'tinggi' => 'bg-rose-100 text-rose-700',
                'sedang' => 'bg-amber-100 text-amber-800',
                default => 'bg-emerald-100 text-emerald-800',
            };
            $selectedPic = $selectedAction->assignedUser?->name ?? 'Belum ditugaskan';
            $selectedInstansi = $selectedAction->assignedUser?->organization?->name ?? 'Kab. Bengkulu Tengah';
        @endphp
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 card-shadow space-y-4">
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h3 class="text-base font-bold text-slate-900">{{ $selectedAction->title }}</h3>
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold {{ $selectedPriorityClass }}">{{ $selectedPriority }}</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600">{{ $selectedAction->description ?: 'Tidak ada deskripsi.' }}</p>
                </div>
                <span class="text-base font-bold text-slate-900">{{ $selectedAction->progress_pct }}%</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-100 text-xs">
                <div class="flex items-center justify-between sm:justify-start gap-3">
                    <span class="text-slate-400 font-medium">PIC</span>
                    <span class="font-bold text-slate-800">{{ $selectedPic }} ({{ $selectedInstansi }})</span>
                </div>
                <div class="flex items-center justify-between sm:justify-start gap-3">
                    <span class="text-slate-400 font-medium">Tenggat</span>
                    <span class="font-bold text-rose-600">{{ $selectedAction->deadline ? $selectedAction->deadline->locale('id')->translatedFormat('j M Y') : '-' }}</span>
                </div>
                <div class="flex items-center justify-between sm:justify-start gap-3">
                    <span class="text-slate-400 font-medium">Bukti</span>
                    @if($selectedAction->evidence_path)
                        <a href="{{ route('actions.evidence', $selectedAction) }}" target="_blank" class="font-bold text-brand-700 hover:text-brand-800">Lihat bukti</a>
                    @else
                        <span class="font-bold text-slate-800">Belum</span>
                    @endif
                </div>
            </div>
            @if($selectedAction->policy_case_id)
                <div class="text-xs text-slate-600 bg-slate-50 border border-slate-100 rounded-xl px-3 py-2">
                    Terkait kasus kebijakan #{{ $selectedAction->policy_case_id }}
                </div>
            @endif
            @if(!empty($impactComparison))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs border border-slate-100 rounded-xl p-3 bg-slate-50/50">
                    <div>
                        <div class="font-bold text-slate-700 mb-1">Sebelum tenggat</div>
                        @if($impactComparison['before'])
                            <div>{{ $impactComparison['before']->commodity_name }}: Rp {{ number_format($impactComparison['before']->price, 0, ',', '.') }} / stok {{ number_format($impactComparison['before']->stock, 0, ',', '.') }}</div>
                        @else
                            <div class="text-slate-400">Belum ada data</div>
                        @endif
                    </div>
                    <div>
                        <div class="font-bold text-slate-700 mb-1">Sesudah tenggat</div>
                        @if($impactComparison['after'])
                            <div>{{ $impactComparison['after']->commodity_name }}: Rp {{ number_format($impactComparison['after']->price, 0, ',', '.') }} / stok {{ number_format($impactComparison['after']->stock, 0, ',', '.') }}</div>
                        @else
                            <div class="text-slate-400">Belum ada data</div>
                        @endif
                    </div>
                </div>
            @endif
            <form action="{{ route('actions.status', $selectedAction->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3 pt-1">
                @csrf
                <input type="hidden" name="pic_id" value="{{ $filterPic }}">
                <input type="hidden" name="priority" value="{{ $filterPriority }}">
                <input type="hidden" name="kecamatan_id" value="{{ $filterRegion }}">
                <input type="hidden" name="view" value="{{ $viewMode }}">
                <input type="hidden" name="mobile" value="{{ $mobileTab }}">
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <input id="taskProgressSlider" name="progress_pct" type="range" min="0" max="100" step="10" value="{{ $selectedAction->progress_pct }}" class="w-full sm:flex-1 accent-brand-600">
                    <button type="submit" class="w-full sm:w-auto bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow-xs transition-colors">Update progres</button>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Unggah bukti pelaksanaan (foto/PDF, max 2 MB)</label>
                    <input type="file" name="evidence" accept=".jpg,.jpeg,.png,.pdf" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-xs">
                </div>
            </form>
            <form action="{{ route('actions.destroy', $selectedAction->id) }}" method="POST" onsubmit="return confirm('Hapus penugasan ini?')" class="flex justify-end">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Hapus penugasan</button>
            </form>
        </div>
    @endif

<!-- Modal Tambah Tindak Lanjut -->
<div id="modal-add-action" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Tambah Rencana Tindak Lanjut</h3>
            <button type="button" onclick="closeAddActionModal()" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
        </div>
        <form action="{{ route('actions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Judul Intervensi / Tindakan <span class="text-red-500">*</span></label>
                <input type="text" name="title" placeholder="Contoh: Operasi Pasar Murah Beras SPHP" required
                       class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Deskripsi & Rincian</label>
                <textarea name="description" rows="2" placeholder="Uraian langkah intervensi dan koordinasi..."
                          class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tingkat Prioritas</label>
                    <select name="priority" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
                        <option value="tinggi">Tinggi</option>
                        <option value="sedang" selected>Sedang</option>
                        <option value="rendah">Rendah</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Awal</label>
                    <select name="status" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
                        <option value="antrean" selected>Antrean</option>
                        <option value="dalam_proses">Dalam Proses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">PIC Penanggung Jawab</label>
                    <select name="assigned_to" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
                        <option value="">Pilih Petugas PIC</option>
                        @foreach($officers as $off)
                            <option value="{{ $off->id }}">{{ $off->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tenggat Waktu</label>
                    <input type="date" name="deadline" value="2026-10-31" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Lokasi Titik Pantau</label>
                <select name="target_point_id" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
                    <option value="">Semua Lokasi / Umum</option>
                    @foreach($points as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Bukti pelaksanaan (opsional)</label>
                <input type="file" name="evidence" accept=".jpg,.jpeg,.png,.pdf" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-800">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeAddActionModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-700 font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl font-semibold hover:bg-brand-700">Simpan Tindak Lanjut</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddActionModal() { document.getElementById('modal-add-action').classList.remove('hidden'); }
    function closeAddActionModal() { document.getElementById('modal-add-action').classList.add('hidden'); }
</script>
@endsection
