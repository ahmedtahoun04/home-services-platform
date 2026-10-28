<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ServiceController extends Controller
{
    /** GET /admin/services (includes inactive services) */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $services = Service::query()
            ->search($filters['search'] ?? null)
            ->category($filters['category'] ?? null)
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return ServiceResource::collection($services);
    }

    /** POST /admin/services */
    public function store(ServiceRequest $request): JsonResponse
    {
        $service = Service::create($request->validated())->refresh();

        return (new ServiceResource($service))
            ->response()
            ->setStatusCode(201);
    }

    /** GET /admin/services/{service} */
    public function show(Service $service): ServiceResource
    {
        return new ServiceResource($service);
    }

    /** PUT/PATCH /admin/services/{service} */
    public function update(ServiceRequest $request, Service $service): ServiceResource
    {
        $service->update($request->validated());

        return new ServiceResource($service->refresh());
    }

    /** DELETE /admin/services/{service} */
    public function destroy(Service $service): Response
    {
        $service->delete();

        return response()->noContent();
    }
}
