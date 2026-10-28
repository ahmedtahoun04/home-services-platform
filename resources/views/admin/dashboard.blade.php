@extends('layouts.app')

@section('title', 'Admin Dashboard - Homely')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-2xl font-bold text-slate-900 mb-1">Admin Dashboard</h1>
    <p class="text-sm text-slate-500 mb-8">
        Signed in as {{ auth()->user()->name }}. Service and booking management pages
        will be built on top of the secured admin APIs.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Services</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $serviceCount }}</p>
        </div>
    </div>
</section>
@endsection
