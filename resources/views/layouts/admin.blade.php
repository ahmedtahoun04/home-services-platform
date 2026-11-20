<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - homely')</title>
    <!-- Tailwind CSS CDN (same color tokens as the customer layout) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        tealPrimary: '#0d9488',
                        tealHover: '#0f766e',
                        darkSlate: '#0f172a',
                        lightBg: '#f8fafc'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-lightBg font-sans text-slate-800 min-h-screen">

@php
    $navActive = 'bg-tealPrimary text-white';
    $navIdle = 'text-slate-300 hover:bg-slate-800';
@endphp

<div class="flex min-h-screen">

    <!-- Sidebar (desktop) -->
    <aside class="hidden md:flex md:w-60 flex-col bg-darkSlate text-slate-200">
        <div class="h-16 px-6 flex items-center space-x-2 border-b border-slate-700">
            <div class="w-8 h-8 bg-tealPrimary rounded-full flex items-center justify-center text-white font-bold text-lg">h</div>
            <span class="font-bold text-lg text-white">homely<span class="text-tealPrimary">.</span></span>
            <span class="text-xs font-medium text-slate-400">admin</span>
        </div>

        <nav class="flex-1 px-3 py-6 space-y-1 text-sm font-medium">
            <a href="{{ route('admin.dashboard') }}"
               class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? $navActive : $navIdle }}">Dashboard</a>
            <a href="{{ route('admin.pages.services') }}"
               class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.pages.services') ? $navActive : $navIdle }}">Services</a>
            <a href="{{ route('admin.pages.bookings') }}"
               class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.pages.bookings') ? $navActive : $navIdle }}">Bookings</a>
        </nav>

        <div class="px-3 py-4 border-t border-slate-700 text-sm">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">View website</a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">

        <!-- Top bar -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between">
            <!-- Mobile navigation -->
            <nav class="md:hidden flex items-center space-x-4 text-sm font-medium">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-tealPrimary' : 'text-slate-600' }}">Dashboard</a>
                <a href="{{ route('admin.pages.services') }}" class="{{ request()->routeIs('admin.pages.services') ? 'text-tealPrimary' : 'text-slate-600' }}">Services</a>
                <a href="{{ route('admin.pages.bookings') }}" class="{{ request()->routeIs('admin.pages.bookings') ? 'text-tealPrimary' : 'text-slate-600' }}">Bookings</a>
            </nav>
            <div class="hidden md:block text-sm text-slate-500">Administration</div>

            <div class="flex items-center space-x-4">
                <span class="text-sm text-slate-600">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-slate-700 hover:text-tealPrimary transition">Logout</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>

<!-- Toast messages -->
<div id="toasts" class="fixed top-4 right-4 z-50 space-y-2"></div>

@verbatim
<script>
    // Shared helpers for every admin page.

    // Message shown when the server did not send one (see docs/API.md).
    function fallbackMessage(status) {
        if (status === 0) { return 'Network error. Check your connection and try again.'; }
        if (status === 401) { return 'Please log in again.'; }
        if (status === 403) { return 'You are not allowed to do this.'; }
        if (status === 404) { return 'Resource not found.'; }
        if (status === 409) { return 'This action conflicts with existing data.'; }
        if (status === 419) { return 'Your session expired. Please reload the page and try again.'; }
        if (status === 422) { return 'Please check the entered data.'; }
        if (status === 429) { return 'Too many requests. Please wait a moment.'; }
        if (status >= 500) { return 'Server error. Please try again later.'; }
        return 'Something went wrong.';
    }

    // Calls a JSON endpoint with the session cookie and the CSRF token.
    // Always returns { ok, status, data } and data.message is always filled when
    // the request failed, so pages can show it directly.
    window.adminApi = async function (url, options) {
        options = options || {};
        const headers = Object.assign({
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }, options.headers || {});

        let body = options.body;
        if (body && typeof body !== 'string') {
            headers['Content-Type'] = 'application/json';
            body = JSON.stringify(body);
        }

        let res;
        try {
            res = await fetch(url, {
                method: options.method || 'GET',
                headers: headers,
                body: body,
                credentials: 'same-origin'
            });
        } catch (e) {
            return { ok: false, status: 0, data: { message: fallbackMessage(0) } };
        }

        let data = null;
        if (res.status !== 204) {
            try { data = await res.json(); } catch (e) { data = null; }
        }

        if (res.status === 401) {
            window.location.href = '/login';
        }

        if (!res.ok && (!data || !data.message)) {
            data = Object.assign({}, data, { message: fallbackMessage(res.status) });
        }

        return { ok: res.ok, status: res.status, data: data };
    };

    // The first validation message of a 422 response, or the general message.
    window.adminErrorText = function (res) {
        if (res.data && res.data.errors) {
            const first = Object.keys(res.data.errors)[0];
            if (first && res.data.errors[first][0]) { return res.data.errors[first][0]; }
        }
        return (res.data && res.data.message) || fallbackMessage(res.status);
    };

    // Escapes text before it is placed inside HTML (prevents XSS).
    window.adminEsc = function (value) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
        return String(value === null || value === undefined ? '' : value)
            .replace(/[&<>"']/g, function (c) { return map[c]; });
    };

    // Small notification in the top right corner.
    window.adminToast = function (message, type) {
        const box = document.createElement('div');
        const color = type === 'error' ? 'bg-red-600' : 'bg-emerald-600';
        box.className = color + ' text-white text-sm px-4 py-3 rounded-lg shadow-lg max-w-sm';
        box.textContent = message;
        document.getElementById('toasts').appendChild(box);
        setTimeout(function () { box.remove(); }, 4500);
    };
</script>
@endverbatim

@stack('scripts')

</body>
</html>
