@php
    $isSelected = $selectedAction && $selectedAction->id === $act->id;
    $priorityClass = match ($act->priority) {
        'tinggi' => 'bg-rose-100 text-rose-700',
        'sedang' => 'bg-amber-100 text-amber-800',
        default => 'bg-emerald-100 text-emerald-800',
    };
    $priorityLabel = match ($act->priority) {
        'tinggi' => 'Tinggi',
        'sedang' => 'Sedang',
        default => 'Rendah',
    };
    $avatarPalette = ['bg-rose-100 text-rose-700', 'bg-sky-100 text-sky-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700', 'bg-indigo-100 text-indigo-700'];
    $avatarClass = $avatarPalette[($act->assigned_to ?? 0) % count($avatarPalette)];
    $picName = $act->assignedUser?->name ?? 'Belum ditugaskan';
    $instansi = $act->assignedUser?->organization?->name ?? 'Kab. Bengkulu Tengah';
    $lampiran = $act->notes ? 1 : 0;
    $query = array_merge(request()->except('selected'), ['selected' => $act->id]);
@endphp
<a href="{{ route('actions.index', $query) }}"
   class="task-card block bg-white rounded-2xl p-4 border-2 {{ $isSelected ? 'border-brand-600 shadow-md' : 'border-transparent shadow-xs hover:border-slate-300' }} transition-all space-y-3">
    <div class="flex items-center justify-between">
        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold {{ $priorityClass }}">{{ $priorityLabel }}</span>
        <span class="text-xs text-slate-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
            {{ $lampiran }}
        </span>
    </div>
    <div>
        <h4 class="font-bold text-slate-900 text-sm">{{ $act->title }}</h4>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $act->description }}</p>
    </div>
    <div class="flex items-center justify-between pt-1">
        <div class="flex items-center gap-2 min-w-0">
            <div class="w-7 h-7 rounded-full {{ $avatarClass }} font-bold text-xs flex items-center justify-center shrink-0">{{ substr($picName, 0, 1) }}</div>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 leading-tight truncate">{{ $picName }}</div>
                <div class="text-[10px] text-slate-400 truncate">{{ $instansi }}</div>
            </div>
        </div>
        <div class="flex items-center gap-1 text-[11px] font-semibold shrink-0 {{ $act->status === 'selesai' ? 'text-slate-500' : 'text-rose-600' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>{{ $act->deadline ? $act->deadline->locale('id')->translatedFormat('j M Y') : '-' }}</span>
        </div>
    </div>
    @if($act->progress_pct > 0)
        <div class="flex items-center gap-2.5 pt-1">
            <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full bg-brand-600 rounded-full" style="width: {{ $act->progress_pct }}%"></div>
            </div>
            <span class="text-xs font-bold text-slate-600">{{ $act->progress_pct }}%</span>
        </div>
    @endif
</a>
