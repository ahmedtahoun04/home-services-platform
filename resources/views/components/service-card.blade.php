@props([
    'id' => 1,
    'category',
    'rating',
    'title',
    'description' => '',
    'price'
])

<div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
    <div>
        <!-- Category & Rating Header -->
        <div class="flex justify-between items-center mb-3">
            <span class="text-xs text-slate-500 font-medium">{{ $category }}</span>
            <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md font-semibold">⭐ {{ number_format($rating, 1) }}</span>
        </div>

        <!-- Service Icon Placeholder -->
        <div class="h-36 bg-slate-50 rounded-lg flex items-center justify-center mb-4 border border-slate-100">
            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.11a2 2 0 00-1.922 1.487"></path>
            </svg>
        </div>

        <!-- Title & Description -->
        <h3 class="font-bold text-slate-800 text-base mb-1">{{ $title }}</h3>
        <p class="text-slate-500 text-xs line-clamp-2 mb-4">{{ $description }}</p>
    </div>

    <!-- Pricing and CTA -->
    <div class="flex items-center justify-between border-t border-slate-100 pt-4">
        <span class="text-lg font-extrabold text-slate-900">${{ number_format($price, 0) }}</span>
        
        <!-- Book Now Button linked to Service Details -->
        <a href="{{ route('service.details', ['id' => $id]) }}" 
           class="bg-tealPrimary hover:bg-tealHover text-white px-4 py-2 rounded-lg text-xs font-semibold transition inline-block">
            Book Now
        </a>
    </div>
</div>