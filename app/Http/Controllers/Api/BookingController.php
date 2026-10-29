<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function myBookings(Request $request)
    {
        $bookings = Booking::with('service')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return BookingResource::collection($bookings);
    }

    public function store(BookingRequest $request): JsonResponse
    {
        $booking = Booking::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'status'  => 'Pending',
        ]);

        $booking->load('service');

        return (new BookingResource($booking))
            ->additional(['message' => 'Booking created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function index(Request $request)
    {
        $query = Booking::with(['user', 'service'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return BookingResource::collection($query->paginate(10));
    }

    public function updateStatus(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['Pending', 'Approved', 'Completed'])],
        ]);

        $booking->update(['status' => $validated['status']]);
        $booking->load(['user', 'service']);

        return (new BookingResource($booking))
            ->additional(['message' => 'Booking status updated successfully.'])
            ->response();
    }
}
