@php
    $showFilters = $showFilters ?? true;
@endphp
<section class="bg-white border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-6 pb-8 sm:pt-8 sm:pb-10">
        <nav class="flex items-center gap-1.5 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('home', $publicFilterQuery) }}" class="hover:text-ink">Beranda</a>
            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-ink">{{ $title }}</span>
        </nav>

        <div class="mt-4 flex flex-col lg:flex-row lg:items-end justify-between gap-3">
            <div>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-ink">{{ $title }}</h1>
                <p class="mt-2 text-base text-slate-600 max-w-2xl">{{ $description }}</p>
            </div>
            @if($showFilters)
                <span class="inline-flex self-start lg:self-auto items-center gap-2 text-xs font-medium text-slate-600 bg-slate-100 px-3 py-1.5 rounded-full shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                    Data {{ $shortDateLabel }}
                    @if($lastUpdated)
                        <span class="text-slate-300">|</span> {{ $lastUpdated->format('H:i') }} WIB
                    @endif
                </span>
            @endif
        </div>

        @if($showFilters)
            <form method="GET" action="{{ url()->current() }}" class="mt-6 rounded-2xl bg-slate-50 ring-1 ring-slate-200/70 p-3 sm:p-4">
                @foreach(request()->except(['date', 'kecamatan_id', 'market_id', 'commodity_id', 'page']) as $key => $value)
                    @if(! is_array($value) && $value !== null && $value !== '')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                @include('public.partials.filter-fields', ['inline' => true])
                @if($hasActiveFilters)
                    <div class="mt-3 flex justify-end">
                        <a href="{{ url()->current() }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Reset filter</a>
                    </div>
                @endif
            </form>
        @endif
    </div>
</section>
