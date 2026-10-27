@extends('layouts.app')

@section('title', 'Sign Up - Homely')

@section('content')
<section class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-1">Create your account</h1>
        <p class="text-sm text-slate-500 mb-6">Book trusted home services in a few clicks.</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password (min. 8 characters)</label>
                <input id="password" type="password" name="password" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
                @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
            </div>

            <button type="submit" class="w-full bg-tealPrimary hover:bg-tealHover text-white font-semibold py-2.5 rounded-lg transition">
                Sign up
            </button>
        </form>

        <p class="text-sm text-slate-500 mt-6 text-center">
            Already have an account?
            <a href="{{ route('login') }}" class="text-tealPrimary font-medium hover:underline">Log in</a>
        </p>
    </div>
</section>
@endsection
