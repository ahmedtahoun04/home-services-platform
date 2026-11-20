<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /** GET /bookings (customer: only the logged-in customer's bookings) */
    public function myBookings(Request $request)
    {
        $bookings = Booking::with('service')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return BookingResource::collection($bookings);
    }

    /** POST /bookings (customer) */
    public function store(BookingRequest $request): JsonResponse
    {
        $booking = new Booking($request->validated());
        // Set by the server, never from request input:
        $booking->user_id = $request->user()->id;
        $booking->status = Booking::STATUS_PENDING;
        $booking->save();

        $booking->load('service');

        return (new BookingResource($booking))
            ->additional(['message' => 'Booking created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    /** GET /admin/bookings?status=Pending (admin) */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'service'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return BookingResource::collection($query->paginate(10));
    }

    /** PATCH /admin/bookings/{booking}/status (admin) */
    public function updateStatus(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(Booking::STATUSES)],
        ]);

        $newStatus = $validated['status'];

        // Only Pending -> Approved -> Completed is allowed (no skipping, no going back).
        if (! $booking->canMoveTo($newStatus)) {
            throw ValidationException::withMessages([
                'status' => ["A {$booking->status} booking cannot be changed to {$newStatus}."],
            ]);
        }

        $booking->status = $newStatus;
        $booking->save();
        $booking->load(['user', 'service']);

        return (new BookingResource($booking))
            ->additional(['message' => 'Booking status updated successfully.'])
            ->response();
    }
}
