@extends('layouts.public')

@section('title', 'Lapor Harga - PANTAU HARGA Bengkulu Tengah')

@section('content')
    @include('public.partials.page-header', [
        'title' => 'Lapor Harga',
        'description' => 'Bantu petugas memantau harga dengan melaporkan harga yang Anda temui di pasar.',
        'showFilters' => false,
    ])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        @include('public.partials.report-form')
    </div>
@endsection
