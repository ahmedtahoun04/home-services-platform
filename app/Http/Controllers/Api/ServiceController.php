<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends Controller
{
    /** GET /api/services?search=pipe&category=Plumbing */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $services = Service::active()
            ->search($filters['search'] ?? null)
            ->category($filters['category'] ?? null)
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return ServiceResource::collection($services);
    }

    /** GET /api/services/{service} (inactive services are hidden) */
    public function show(Service $service): ServiceResource
    {
        abort_unless($service->is_active, 404);

        return new ServiceResource($service);
    }
}
