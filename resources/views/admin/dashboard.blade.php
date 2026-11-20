@extends('layouts.admin')

@section('title', 'Admin Dashboard - homely')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900 mb-1">Admin Dashboard</h1>
    <p class="text-sm text-slate-500 mb-8">Signed in as {{ auth()->user()->name }}.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Services</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $serviceCount }}</p>
            <a href="{{ route('admin.pages.services') }}" class="inline-block mt-4 text-sm font-semibold text-tealPrimary hover:underline">Manage services</a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bookings</p>
            <p class="text-sm text-slate-600 mt-2">Review booking requests and move them from Pending to Approved to Completed.</p>
            <a href="{{ route('admin.pages.bookings') }}" class="inline-block mt-4 text-sm font-semibold text-tealPrimary hover:underline">Manage bookings</a>
        </div>
    </div>
@endsection
