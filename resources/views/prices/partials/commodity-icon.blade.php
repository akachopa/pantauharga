@php
    $label = strtolower((string) ($name ?? ''));
    $type = 'egg';
    if (str_contains($label, 'beras')) {
        $type = 'rice';
    } elseif (str_contains($label, 'gula')) {
        $type = 'sugar';
    } elseif (str_contains($label, 'minyak')) {
        $type = 'oil';
    } elseif (str_contains($label, 'cabai') || str_contains($label, 'cabe')) {
        $type = 'chili';
    } elseif (str_contains($label, 'sawit') || str_contains($label, 'tbs')) {
        $type = 'palm';
    }
@endphp
@if($type === 'rice')
    <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center shrink-0">
        <svg class="w-7 h-7" viewBox="0 0 36 36" fill="none">
            <ellipse cx="18" cy="20" rx="11" ry="6" fill="#F8FAFC" stroke="#CBD5E1" stroke-width="1.5"/>
            <path d="M7 20c0 5.5 4.9 10 11 10s11-4.5 11-10H7z" fill="#D97706"/>
            <circle cx="15" cy="18" r="1.2" fill="#94A3B8"/><circle cx="19" cy="17" r="1.2" fill="#94A3B8"/><circle cx="21" cy="19" r="1.2" fill="#94A3B8"/>
        </svg>
    </div>
@elseif($type === 'sugar')
    <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center shrink-0">
        <svg class="w-7 h-7" viewBox="0 0 36 36" fill="none">
            <path d="M8 25c3-8 7-12 10-12s7 4 10 12H8z" fill="#F1F5F9" stroke="#CBD5E1" stroke-width="1.5"/>
            <rect x="14" y="16" width="4" height="4" rx="1" fill="#E2E8F0"/>
            <rect x="19" y="18" width="4" height="4" rx="1" fill="#CBD5E1"/>
        </svg>
    </div>
@elseif($type === 'oil')
    <div class="w-11 h-11 rounded-xl bg-yellow-50 border border-yellow-200/70 flex items-center justify-center shrink-0">
        <svg class="w-7 h-7" viewBox="0 0 36 36" fill="none">
            <rect x="13" y="11" width="10" height="19" rx="3" fill="#FACC15" stroke="#EAB308" stroke-width="1.5"/>
            <rect x="15" y="6" width="6" height="4" rx="1" fill="#DC2626"/>
            <path d="M13 17h10" stroke="#CA8A04" stroke-width="1.2"/>
        </svg>
    </div>
@elseif($type === 'chili')
    <div class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200/70 flex items-center justify-center shrink-0">
        <svg class="w-7 h-7" viewBox="0 0 36 36" fill="none">
            <path d="M10 25c7-1 13-6 15-14" stroke="#DC2626" stroke-width="4" stroke-linecap="round"/>
            <path d="M13 27c7 0 12-4 15-11" stroke="#EF4444" stroke-width="3.2" stroke-linecap="round"/>
            <path d="M25 11c1-2 3-3 4-2" stroke="#16A34A" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </div>
@elseif($type === 'palm')
    <div class="w-11 h-11 rounded-xl bg-orange-50 border border-orange-200/70 flex items-center justify-center shrink-0">
        <svg class="w-7 h-7" viewBox="0 0 36 36" fill="none">
            <path d="M18 9c-1-3 1-5 3-5M18 9c-2-2-5-2-7 0" stroke="#15803D" stroke-width="1.8" stroke-linecap="round"/>
            <ellipse cx="18" cy="21" rx="10" ry="10" fill="#7C2D12"/>
            <circle cx="14" cy="17" r="2.2" fill="#EA580C"/><circle cx="19" cy="15.5" r="2.2" fill="#DC2626"/><circle cx="23" cy="19" r="2.2" fill="#EA580C"/>
            <circle cx="15" cy="22.5" r="2.2" fill="#DC2626"/><circle cx="20" cy="21.5" r="2.2" fill="#F97316"/><circle cx="17.5" cy="27" r="2.2" fill="#EA580C"/><circle cx="23" cy="25" r="2.2" fill="#DC2626"/><circle cx="12" cy="26" r="2" fill="#9A3412"/>
        </svg>
    </div>
@else
    <div class="w-11 h-11 rounded-xl bg-orange-50 border border-orange-200/70 flex items-center justify-center shrink-0">
        <svg class="w-7 h-7" viewBox="0 0 36 36" fill="none">
            <ellipse cx="15" cy="21" rx="5.5" ry="7" fill="#FDBA74" stroke="#EA580C" stroke-width="1.2"/>
            <ellipse cx="22" cy="22" rx="5.5" ry="6.5" fill="#FED7AA" stroke="#EA580C" stroke-width="1.2"/>
        </svg>
    </div>
@endif
