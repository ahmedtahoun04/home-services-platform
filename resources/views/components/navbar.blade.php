<header class="bg-white border-b border-slate-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-tealPrimary rounded-full flex items-center justify-center text-white font-bold text-lg">h</div>
            <span class="font-bold text-xl text-slate-900 tracking-tight">homely<span class="text-tealPrimary">.</span></span>
        </div>

        <!-- Nav Links -->
        <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-600">
            <a href="{{ route('home') }}" class="text-tealPrimary font-semibold">Home</a>
            <a href="#services" class="hover:text-tealPrimary transition">Services</a>
            <a href="#track" class="hover:text-tealPrimary transition">Track Order</a>
        </nav>

        <!-- Auth -->
        <div class="flex items-center space-x-4">
            @auth
                <span class="text-sm text-slate-600">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-slate-700 hover:text-tealPrimary transition">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 hover:text-tealPrimary transition">Login</a>
                <a href="{{ route('register') }}" class="bg-tealPrimary hover:bg-tealHover text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Sign Up</a>
            @endauth
        </div>
    </div>
</header>
