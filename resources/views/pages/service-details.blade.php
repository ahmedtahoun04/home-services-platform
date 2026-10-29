@extends('layouts.app')

@section('title', 'Service Details - Homely')

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <!-- Back Link -->
        <a href="{{ route('home') }}" class="inline-flex items-center text-xs font-semibold text-tealPrimary hover:underline mb-6">
            ← Back to All Services
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Left Column: Service Details -->
            <div class="lg:col-span-7 space-y-6">
                
                <div class="flex items-center space-x-3">
                    <span class="text-xs bg-teal-50 text-tealPrimary font-bold px-3 py-1 rounded-full uppercase">Plumbing</span>
                    <span class="text-xs text-slate-500 font-semibold">⭐ 4.9 (120+ reviews)</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Home Plumbing Repair & Inspection</h1>
                
                <!-- Service Banner / Image Placeholder -->
                <div class="h-64 bg-slate-100 rounded-2xl flex items-center justify-center border border-slate-200">
                    <svg class="w-20 h-20 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.11a2 2 0 00-1.922 1.487"></path>
                    </svg>
                </div>

                <div class="space-y-3">
                    <h3 class="text-lg font-bold text-slate-800">What's included in this service?</h3>
                    <ul class="list-disc list-inside text-sm text-slate-600 space-y-1">
                        <li>Comprehensive inspection of all household pipes and fixtures.</li>
                        <li>Fixing minor and major leakages using professional tools.</li>
                        <li>Unclogging sinks and bathroom drains safely.</li>
                        <li>30-day service guarantee included.</li>
                    </ul>
                </div>
            </div>

            <!-- Right Column: Booking Form -->
            <div class="lg:col-span-5">
                <x-booking-form />
            </div>

        </div>

    </div>

@endsection