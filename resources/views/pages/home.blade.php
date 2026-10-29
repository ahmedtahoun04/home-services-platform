@extends('layouts.app')

@section('title', 'Home Services - Homely')

@section('content')

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

            <!-- Left Hero Content -->
            <div class="lg:col-span-7 space-y-6">
                <span class="text-xs font-bold text-tealPrimary uppercase tracking-wider">CONNECTING TRUSTED PROS TO HOME</span>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight">
                    Find Reliable Home <br class="hidden sm:inline" />Services Easily
                </h1>
                <p class="text-slate-500 text-sm sm:text-base max-w-lg">
                    From quick repairs to deep cleaning, book trusted local professionals for all your household needs.
                </p>

                <!-- Search Box -->
                <form method="GET" action="{{ route('home') }}#services"
                      class="bg-white p-2 rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row gap-2">
                    @if ($category)
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif
                    <div class="flex-1 flex items-center px-3 space-x-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="search" value="{{ $search }}" placeholder="What service do you need?" class="w-full text-sm focus:outline-none text-slate-800 placeholder-slate-400" />
                    </div>
                    <button type="submit" class="bg-tealPrimary hover:bg-tealHover text-white px-6 py-3 rounded-lg text-sm font-semibold transition">
                        Search
                    </button>
                </form>

                <!-- Filter Chips -->
                @php
                    $chipActive = 'px-4 py-1.5 bg-tealPrimary text-white text-xs font-semibold rounded-full shadow-sm';
                    $chipIdle = 'px-4 py-1.5 bg-white text-slate-600 hover:bg-slate-100 text-xs font-medium rounded-full border border-slate-200 transition';
                @endphp
                <div class="flex flex-wrap items-center gap-2 pt-2">
                    <a href="{{ route('home', array_filter(['search' => $search])) }}#services"
                       class="{{ $category ? $chipIdle : $chipActive }}">All</a>
                    @foreach ($categories as $cat)
                        <a href="{{ route('home', array_filter(['search' => $search, 'category' => $cat])) }}#services"
                           class="{{ $category === $cat ? $chipActive : $chipIdle }}">{{ $cat }}</a>
                    @endforeach
                </div>
            </div>

            <!-- Right Hero Illustration Card -->
            <div class="lg:col-span-5 flex justify-center">
                <div class="w-full max-w-md bg-teal-50 border border-teal-100 rounded-2xl p-8 flex flex-col items-center justify-center text-center relative">
                    <div class="w-32 h-32 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-16 h-16 text-tealPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-tealPrimary uppercase tracking-wider mb-1">Fast & Reliable</span>
                    <h3 class="text-lg font-bold text-slate-800">Verified Professionals</h3>
                </div>
            </div>

        </div>
    </section>

    <!-- Services Catalog Grid Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-16" id="services">
        <div class="mb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">A little help for every home</h2>
            <p class="text-slate-500 text-sm">Explore our most booked home maintenance services.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($services as $service)
                <x-service-card
                    :id="$service->id"
                    :category="$service->category"
                    :rating="(float) $service->rating"
                    :title="$service->name"
                    :description="$service->description ?? ''"
                    :price="(float) $service->price" />
            @empty
                <p class="col-span-full text-sm text-slate-500">
                    No services found. Try a different search or category.
                </p>
            @endforelse
        </div>
    </section>

@endsection