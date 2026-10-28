<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $search = $filters['search'] ?? null;
        $category = $filters['category'] ?? null;

        $services = Service::active()
            ->search($search)
            ->category($category)
            ->orderBy('id')
            ->get();

        $categories = Service::active()
            ->orderBy('category')
            ->distinct()
            ->pluck('category');

        return view('pages.home', compact('services', 'categories', 'search', 'category'));
    }
}
