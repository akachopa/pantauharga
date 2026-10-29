@extends('layouts.app')

@section('title', 'Paket Kebijakan - PANTAU HARGA')
@section('breadcrumb', 'Paket Kebijakan')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Paket Kebijakan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Ubah temuan menjadi rekomendasi yang terarah.</p>
        </div>
        <div class="hidden sm:flex items-center gap-2.5">
            <button type="button" id="btnPaketBaru" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                <span>Paket Baru</span>
            </button>
            <button type="button" onclick="openUjiRuleModal()" class="bg-white hover:bg-slate-50 text-brand-700 border border-slate-200 font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <span>Uji Rule</span>
            </button>
        </div>
    </div>

    @if($activeTab === 'daftar')
        <div class="lg:hidden grid grid-cols-2 bg-slate-200/70 p-1 rounded-xl">
            <a href="{{ route('policies.index', ['pkg' => $selectedPackage['id'], 'view' => 'daftar']) }}" class="keb-mob-tab py-1.5 rounded-lg text-xs font-semibold text-center {{ $mobileView !== 'detail' ? 'bg-white text-brand-700 shadow-xs' : 'text-slate-600' }}">Daftar Paket</a>
            <a href="{{ route('policies.index', ['pkg' => $selectedPackage['id'], 'view' => 'detail']) }}" class="keb-mob-tab py-1.5 rounded-lg text-xs font-semibold text-center {{ $mobileView === 'detail' ? 'bg-white text-brand-700 shadow-xs' : 'text-slate-600' }}">Detail Rule</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div id="policy-list-col" class="lg:col-span-4 space-y-3.5 {{ $mobileView === 'detail' ? 'hidden lg:block' : '' }}">
                @foreach($policyCards as $card)
                    @php
                        $isSelected = $selectedPackage['id'] === $card['id'];
                        $iconWrap = match ($card['icon_type']) {
                            'trend' => 'bg-rose-50 text-rose-500',
                            'box' => 'bg-blue-50 text-blue-600',
                            default => 'bg-sky-50 text-sky-600',
                        };
                        $iconPath = match ($card['icon_type']) {
                            'trend' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                            'box' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                            default => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                        };
                    @endphp
                    <a href="{{ route('policies.index', ['pkg' => $card['id'], 'view' => 'detail']) }}"
                       class="pkg-card bg-white rounded-2xl p-4 border-2 {{ $isSelected ? 'border-brand-600 bg-emerald-50/20' : 'border-slate-200/70 hover:border-slate-300' }} card-shadow transition-all flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl {{ $iconWrap }} flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/></svg>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">{{ $card['name'] }}</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $card['status'] === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $card['status'] }}</span>
                                </div>
                                <p class="hidden sm:block text-xs text-slate-500 leading-relaxed">{{ $card['description'] }}</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 shrink-0 mt-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endforeach
            </div>

            <div id="policy-detail-col" class="{{ $mobileView === 'detail' ? '' : 'hidden' }} lg:block lg:col-span-8 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/70 card-shadow space-y-5">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900">Rule: {{ $selectedPackage['name'] }}</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Atur kondisi pemicu dan rekomendasi kebijakan untuk paket ini.</p>
                </div>

                <form action="{{ route('policies.rules.store') }}" method="POST" id="policy-rule-form" class="space-y-5">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $selectedPackage['id'] }}">
                    <input type="hidden" name="indicator" value="price_change_pct">
                    <input type="hidden" name="operator" value="gt">
                    <input type="hidden" name="threshold" value="10">
                    <input type="hidden" name="period_days" value="7">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Komoditi</label>
                            <select name="commodity_id" id="ruleKomoditiSelect" class="form-field w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white">
                                @foreach(['Cabai merah', 'Beras medium', 'Minyak goreng', 'Semua komoditi'] as $commodityOption)
                                    <option value="{{ $commodityOption }}" @selected($selectedPackage['commodity'] === $commodityOption)>{{ $commodityOption }}</option>
                                @endforeach
                                @foreach($commodities as $c)
                                    @if(! in_array($c->name, ['Cabai merah', 'Beras medium', 'Minyak goreng', 'Semua komoditi'], true))
                                        <option value="{{ $c->id }}" @selected($c->name === $selectedPackage['commodity'])>{{ $c->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Wilayah</label>
                            <select name="region_id" id="ruleWilayahSelect" class="form-field w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white">
                                @foreach(['Semua wilayah', 'Karang Tinggi', 'Talang Empat'] as $regionOption)
                                    <option value="{{ $regionOption }}" @selected($selectedPackage['region'] === $regionOption)>{{ $regionOption }}</option>
                                @endforeach
                                @foreach($kecamatans as $kec)
                                    @if(! in_array($kec->name, ['Semua wilayah', 'Karang Tinggi', 'Talang Empat'], true))
                                        <option value="{{ $kec->id }}" @selected($kec->name === $selectedPackage['region'])>{{ $kec->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div>
                            <div class="text-xs sm:text-sm font-bold text-slate-900">Kondisi pemicu</div>
                            <div class="text-xs text-slate-500">Semua kondisi harus terpenuhi (AND).</div>
                        </div>
                        <div id="kondisi-list" class="space-y-2 pt-1">
                            @foreach($selectedPackage['conditions'] as $idx => $kondisi)
                                <div class="kondisi-item">
                                    @if($idx > 0)
                                        <div class="flex flex-col items-center py-0.5">
                                            <div class="w-px h-2 bg-brand-300"></div>
                                            <span class="px-3 py-0.5 rounded-full text-[10px] font-bold bg-brand-600 text-white tracking-wider">AND</span>
                                            <div class="w-px h-2 bg-brand-300"></div>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between border border-slate-200 rounded-xl p-3 bg-slate-50/40">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg {{ $kondisi['type'] === 'price' ? 'bg-rose-50 text-rose-500' : 'bg-orange-50 text-orange-500' }} flex items-center justify-center shrink-0">
                                                @if($kondisi['type'] === 'price')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                @endif
                                            </div>
                                            <span class="kondisi-text text-xs sm:text-sm font-semibold text-slate-800">{{ $kondisi['text'] }}</span>
                                        </div>
                                        <button type="button" class="btn-del-kondisi p-1.5 text-slate-400 hover:text-rose-600 rounded-lg" title="Hapus kondisi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="6" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="18" r="1.8"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" onclick="openAddConditionModal()" class="w-full border border-dashed border-slate-300 hover:border-brand-500 rounded-xl py-2.5 text-xs sm:text-sm font-semibold text-brand-700 hover:bg-brand-50/40 transition-colors flex items-center justify-center gap-1.5 mt-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah kondisi</span>
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-bold text-slate-900 mb-1.5">Rekomendasi</label>
                        <textarea name="recommendation" id="ruleRekomendasiInput" rows="2" class="form-field w-full border border-slate-200 rounded-xl p-3 text-xs sm:text-sm text-slate-800">{{ $selectedPackage['recommendation'] }}</textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 border-t border-slate-100">
                        <div class="flex items-center gap-3">
                            <label class="text-xs sm:text-sm font-bold text-slate-900">Persetujuan</label>
                            <select name="approval_level" id="rulePersetujuanSelect" class="form-field border border-slate-200 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-medium text-slate-800 bg-white">
                                <option @selected($selectedPackage['approval'] === 'Pimpinan')>Pimpinan</option>
                                <option @selected($selectedPackage['approval'] === 'Admin Utama')>Admin Utama</option>
                                <option>Tim Satgas Pangan</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 sm:flex items-center gap-2.5">
                            <button type="submit" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs sm:text-sm font-semibold text-slate-700 transition-colors">
                                Simpan draft
                            </button>
                            <button type="button" onclick="openUjiRuleModal()" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-semibold flex items-center justify-center gap-2 shadow-xs transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                <span>Uji rule</span>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="flex items-center gap-2 text-[11px] text-slate-400">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Rekomendasi tidak otomatis menjadi kebijakan atau anggaran.</span>
                </div>
            </div>
        </div>
    @endif

    @if($activeTab === 'terpicu')
        <div class="bg-white rounded-2xl border border-slate-200/70 card-shadow p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Kasus Kebijakan Terpicu Menunggu Persetujuan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Dihasilkan oleh evaluasi data harga live terhadap rule aktif.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                    {{ count($triggeredCases) }} Kasus Aktif
                </span>
            </div>
            <div class="space-y-4">
                @forelse($triggeredCases as $c)
                    <div class="border border-amber-200/80 rounded-2xl p-5 bg-amber-50/20 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800">Kasus #{{ $c['id'] }}</span>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $c['commodity'] }} • {{ $c['point'] }}</h4>
                            </div>
                            <span class="text-xs text-slate-400 font-mono">{{ $c['date'] }}</span>
                        </div>
                        <div class="text-xs text-slate-700">
                            <span class="font-bold text-slate-900">{{ $c['package_name'] }}:</span> {{ $c['observed_value'] }} (ambang {{ $c['threshold'] }})
                        </div>
                        <div class="text-xs text-slate-700">
                            <span class="font-bold text-slate-900">Rekomendasi Tindakan:</span> {{ $c['recommendation'] }}
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <form action="{{ route('policies.cases.decide', $c['id']) }}" method="POST">
                                @csrf
                                <input type="hidden" name="decision" value="rejected">
                                <button type="submit" class="px-3.5 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold">Tolak</button>
                            </form>
                            <form action="{{ route('policies.cases.decide', $c['id']) }}" method="POST">
                                @csrf
                                <input type="hidden" name="decision" value="approved">
                                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-xs">Setujui Intervensi</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-slate-400">Tidak ada rule yang terpicu pada observasi terbaru.</div>
                @endforelse
            </div>
        </div>
    @endif

    @if($activeTab === 'riwayat')
        <div class="bg-white rounded-2xl border border-slate-200/70 card-shadow p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-800">Riwayat Eksekusi Rule Engine</h3>
            <div class="overflow-x-auto border border-slate-100 rounded-xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                            <th class="py-3 px-4">Run ID</th>
                            <th class="py-3 px-4">Waktu Evaluasi</th>
                            <th class="py-3 px-4">Versi Engine</th>
                            <th class="py-3 px-4 text-center">Mode</th>
                            <th class="py-3 px-4 text-center">Hasil Matched</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($evaluationRuns as $run)
                            <tr>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-800">#{{ $run['id'] }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $run['evaluated_at'] }}</td>
                                <td class="py-3.5 px-4 text-slate-800 font-medium">{{ $run['engine'] }}</td>
                                <td class="py-3.5 px-4 text-center font-mono uppercase text-slate-600">{{ $run['mode'] }}</td>
                                <td class="py-3.5 px-4 text-center">{{ $run['matched_count'] }} terdeteksi</td>
                                <td class="py-3.5 px-4 text-center">Selesai</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div id="modal-uji-rule" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <h3 id="uji-rule-title" class="text-sm font-bold text-slate-900">Hasil Simulasi Uji Rule — {{ $selectedPackage['name'] }}</h3>
                <button type="button" onclick="closePolicyModal('modal-uji-rule')" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
            </div>
            <div id="uji-rule-body" class="space-y-3"></div>
        </div>
    </div>

    <div id="modal-add-condition" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Kondisi Pemicu (AND)</h3>
                <button type="button" onclick="closePolicyModal('modal-add-condition')" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
            </div>
            <form id="add-kondisi-form" class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori Indikator</label>
                    <select id="kondisiTipe" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                        <option value="price">Indikator Harga</option>
                        <option value="stock">Indikator Stok & Pasokan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Parameter Kondisi</label>
                    <input id="kondisiTeks" type="text" placeholder="Contoh: Harga di atas HET > 5%" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closePolicyModal('modal-add-condition')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-semibold">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="policy-toast" class="hidden fixed bottom-6 right-6 z-50 max-w-sm px-4 py-3 rounded-xl bg-slate-900 text-white text-xs font-semibold shadow-xl"></div>
@endsection

@push('scripts')
<script>
    const selectedPackageName = @json($selectedPackage['name']);
    const defaultConditions = @json(array_column($selectedPackage['conditions'], 'text'));

    function closePolicyModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openPolicyModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function showPolicyToast(message) {
        const toast = document.getElementById('policy-toast');
        toast.textContent = message;
        toast.classList.remove('hidden');
        window.clearTimeout(showPolicyToast.timer);
        showPolicyToast.timer = window.setTimeout(function () {
            toast.classList.add('hidden');
        }, 2800);
    }

    function selectValue(id) {
        const el = document.getElementById(id);
        if (!el) {
            return '';
        }
        if (window.jQuery && jQuery(el).hasClass('select2-hidden-accessible')) {
            return jQuery(el).val() || '';
        }
        return el.value || el.options[el.selectedIndex]?.text || '';
    }

    function openUjiRuleModal() {
        const komoditi = selectValue('ruleKomoditiSelect') || @json($selectedPackage['commodity']);
        const wilayah = selectValue('ruleWilayahSelect') || @json($selectedPackage['region']);
        const rekomendasi = document.getElementById('ruleRekomendasiInput')?.value || @json($selectedPackage['recommendation']);
        const conditions = Array.from(document.querySelectorAll('.kondisi-text')).map(function (el) {
            return el.textContent.trim();
        });
        if (!conditions.length) {
            defaultConditions.forEach(function (text) { conditions.push(text); });
        }

        document.getElementById('uji-rule-title').textContent = 'Hasil Simulasi Uji Rule — ' + selectedPackageName;
        document.getElementById('uji-rule-body').innerHTML =
            '<div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-1">' +
                '<div class="font-bold text-sm">Rule Terpicu pada Data Simulasi (28 Okt 2026)</div>' +
                '<p>Komoditi: <strong>' + komoditi + '</strong> • Wilayah: <strong>' + wilayah + '</strong></p>' +
            '</div>' +
            '<div class="text-xs text-slate-600 space-y-1.5">' +
                '<div class="font-semibold text-slate-800">Evaluasi Kondisi (AND):</div>' +
                conditions.map(function (text) {
                    return '<div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span><span>' + text + ' — <strong>Terpenuhi</strong></span></div>';
                }).join('') +
            '</div>' +
            '<div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">' +
                '<div class="text-slate-400 font-semibold uppercase text-[10px]">Output Rekomendasi:</div>' +
                '<div class="font-bold text-slate-800 mt-0.5">' + rekomendasi + '</div>' +
            '</div>' +
            '<div class="pt-2 flex justify-end">' +
                '<button type="button" onclick="closePolicyModal(\'modal-uji-rule\')" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-semibold">Selesai</button>' +
            '</div>';

        openPolicyModal('modal-uji-rule');
    }

    function openAddConditionModal() {
        document.getElementById('add-kondisi-form').reset();
        openPolicyModal('modal-add-condition');
        if (window.initSelect2) {
            window.initSelect2(document.getElementById('modal-add-condition'));
        }
    }

    document.getElementById('btnPaketBaru')?.addEventListener('click', function () {
        showPolicyToast('Mode pembuatan paket kebijakan baru siap digunakan');
    });

    document.getElementById('add-kondisi-form')?.addEventListener('submit', function (event) {
        event.preventDefault();
        const tipe = document.getElementById('kondisiTipe').value;
        const teks = document.getElementById('kondisiTeks').value.trim();
        if (!teks) {
            return;
        }

        const list = document.getElementById('kondisi-list');
        const iconClass = tipe === 'price' ? 'bg-rose-50 text-rose-500' : 'bg-orange-50 text-orange-500';
        const iconPath = tipe === 'price'
            ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'
            : 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4';
        const connector = list.children.length
            ? '<div class="flex flex-col items-center py-0.5"><div class="w-px h-2 bg-brand-300"></div><span class="px-3 py-0.5 rounded-full text-[10px] font-bold bg-brand-600 text-white tracking-wider">AND</span><div class="w-px h-2 bg-brand-300"></div></div>'
            : '';

        const item = document.createElement('div');
        item.className = 'kondisi-item';
        item.innerHTML = connector +
            '<div class="flex items-center justify-between border border-slate-200 rounded-xl p-3 bg-slate-50/40">' +
                '<div class="flex items-center gap-3">' +
                    '<div class="w-9 h-9 rounded-lg ' + iconClass + ' flex items-center justify-center shrink-0">' +
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' + iconPath + '"/></svg>' +
                    '</div>' +
                    '<span class="kondisi-text text-xs sm:text-sm font-semibold text-slate-800">' + teks + '</span>' +
                '</div>' +
                '<button type="button" class="btn-del-kondisi p-1.5 text-slate-400 hover:text-rose-600 rounded-lg" title="Hapus kondisi">' +
                    '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="6" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="18" r="1.8"/></svg>' +
                '</button>' +
            '</div>';
        list.appendChild(item);
        closePolicyModal('modal-add-condition');
        showPolicyToast('Kondisi pemicu baru ditambahkan');
    });

    document.getElementById('kondisi-list')?.addEventListener('click', function (event) {
        const button = event.target.closest('.btn-del-kondisi');
        if (!button) {
            return;
        }
        const items = document.querySelectorAll('.kondisi-item');
        if (items.length <= 1) {
            showPolicyToast('Minimal 1 kondisi pemicu harus dipertahankan');
            return;
        }
        button.closest('.kondisi-item').remove();
        const first = document.querySelector('.kondisi-item');
        const connector = first?.querySelector('.flex.flex-col.items-center');
        if (connector) {
            connector.remove();
        }
        showPolicyToast('Kondisi pemicu dihapus');
    });
</script>
@endpush
