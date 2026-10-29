@extends('layouts.app')

@section('title', 'My Bookings - Homely')

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">My Bookings</h1>
            <p class="text-slate-500 text-sm">Manage and track your service appointments.</p>
        </div>

        <!-- Bookings List / Cards -->
        <div class="space-y-4">
            
            <!-- Sample Booking Item 1 (Pending/Confirmed) -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-teal-50 text-tealPrimary rounded-xl flex items-center justify-center shrink-0 font-bold">
                        🛠️
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="font-bold text-slate-900 text-base">Home Plumbing Repair</h3>
                            <span class="bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 rounded-full font-medium">Pending</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Date: Oct 31, 2026 • Time: 09:00 AM</p>
                        <p class="text-xs text-slate-600 mt-1">Address: 123 Main Street, Apt 4B</p>
                    </div>
                </div>

                <div class="flex items-center justify-between md:justify-end space-x-4 border-t md:border-t-0 border-slate-100 pt-4 md:pt-0">
                    <div class="text-left md:text-right">
                        <span class="text-xs text-slate-400 block">Total Amount</span>
                        <span class="text-lg font-extrabold text-slate-900">$150</span>
                    </div>
                    <button class="text-xs text-rose-600 hover:text-rose-700 font-semibold border border-rose-200 hover:bg-rose-50 px-3 py-2 rounded-lg transition">
                        Cancel Booking
                    </button>
                </div>
            </div>

            <!-- Sample Booking Item 2 (Completed) -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 opacity-85">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center shrink-0 font-bold">
                        🧹
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="font-bold text-slate-900 text-base">Deep Home Cleaning</h3>
                            <span class="bg-emerald-100 text-emerald-800 text-xs px-2.5 py-0.5 rounded-full font-medium">Completed</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Date: Oct 15, 2026 • Time: 01:00 PM</p>
                        <p class="text-xs text-slate-600 mt-1">Address: 123 Main Street, Apt 4B</p>
                    </div>
                </div>

                <div class="flex items-center justify-between md:justify-end space-x-4 border-t md:border-t-0 border-slate-100 pt-4 md:pt-0">
                    <div class="text-left md:text-right">
                        <span class="text-xs text-slate-400 block">Total Amount</span>
                        <span class="text-lg font-extrabold text-slate-900">$180</span>
                    </div>
                    <a href="{{ route('service.details', ['id' => 1]) }}" class="text-xs text-tealPrimary hover:underline font-semibold">
                        Book Again
                    </a>
                </div>
            </div>

        </div>

    </div>

@endsection