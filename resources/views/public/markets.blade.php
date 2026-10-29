@extends('layouts.public')

@section('title', 'Pasar Terdekat - PANTAU HARGA Bengkulu Tengah')

@section('content')
    @include('public.partials.page-header', [
        'title' => 'Pasar Terdekat',
        'description' => 'Bandingkan harga resmi di pasar-pasar terdekat dan temukan yang paling hemat.',
    ])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-10 sm:space-y-12">
        @include('public.partials.market-map')

        @include('public.partials.nearby-markets')
    </div>
@endsection
