@extends('layouts.app')

@section('title', 'Login - Homely')

@section('content')
<section class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-1">Welcome back</h1>
        <p class="text-sm text-slate-500 mb-6">Log in to manage your bookings.</p>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
                @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center space-x-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                <span>Remember me</span>
            </label>

            <button type="submit" class="w-full bg-tealPrimary hover:bg-tealHover text-white font-semibold py-2.5 rounded-lg transition">
                Log in
            </button>
        </form>

        <p class="text-sm text-slate-500 mt-6 text-center">
            No account yet?
            <a href="{{ route('register') }}" class="text-tealPrimary font-medium hover:underline">Sign up</a>
        </p>
    </div>
</section>
@endsection
