<header class="bg-white border-b border-slate-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        
        <!-- Logo & Brand Name -->
        <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-tealPrimary rounded-lg flex items-center justify-center font-bold text-white text-lg">
                h
            </div>
            <span class="font-extrabold text-xl text-slate-900 tracking-tight">homely<span class="text-tealPrimary">.</span></span>
        </a>

        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-600">
            <a href="{{ route('home') }}" class="hover:text-tealPrimary transition">Home</a>
            <a href="{{ route('home') }}#services" class="hover:text-tealPrimary transition">Services</a>
            <a href="{{ route('bookings.view') }}" class="hover:text-tealPrimary transition">My Bookings</a>
        </nav>

        <!-- Auth Section -->
        <div class="flex items-center space-x-4">
            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg font-semibold transition">
                        Admin Dashboard
                    </a>
                @endif
                
                <span class="text-sm font-medium text-slate-700 hidden sm:inline">
                    {{ auth()->user()->name ?? 'Account' }}
                </span>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 font-semibold border border-rose-200 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 px-3 py-2 transition">
                    Login
                </a>
                <a href="{{ route('register') }}" class="text-xs font-semibold bg-tealPrimary hover:bg-tealHover text-white px-4 py-2 rounded-lg transition">
                    Sign Up
                </a>
            @endauth
        </div>

    </div>
</header>