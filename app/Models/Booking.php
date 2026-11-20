<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'Pending';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_COMPLETED = 'Completed';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_COMPLETED,
    ];

    /** The only allowed moves: Pending -> Approved -> Completed. */
    private const NEXT_STATUS = [
        self::STATUS_PENDING => self::STATUS_APPROVED,
        self::STATUS_APPROVED => self::STATUS_COMPLETED,
    ];

    // user_id and status are NOT fillable: the server sets them, so a customer
    // cannot book on behalf of someone else or choose their own status.
    protected $fillable = [
        'service_id',
        'booking_date',
        'booking_time',
        'address',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date:Y-m-d',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function canMoveTo(string $status): bool
    {
        return (self::NEXT_STATUS[$this->status] ?? null) === $status;
    }
}
