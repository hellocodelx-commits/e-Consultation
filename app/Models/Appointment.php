<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['patient_id', 'staff_id', 'availability_slot_id', 'appointment_date', 'start_time', 'end_time', 'reason', 'status'])]
class Appointment extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_DECLINED = 'declined';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_DECLINED,
        self::STATUS_CANCELLED,
        self::STATUS_COMPLETED,
    ];

    private const TRANSITIONS = [
        self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_DECLINED, self::STATUS_CANCELLED],
        self::STATUS_CONFIRMED => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(AvailabilitySlot::class, 'availability_slot_id');
    }

    public function consultation(): HasOne
    {
        return $this->hasOne(ConsultationDetail::class);
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function transitionTo(string $status): void
    {
        $this->update(['status' => $status]);

        if (in_array($status, [self::STATUS_DECLINED, self::STATUS_CANCELLED], true)) {
            $this->releaseSlot();
        }
    }

    public function getTimeRangeAttribute(): string
    {
        return substr($this->start_time, 0, 5).' - '.substr($this->end_time, 0, 5);
    }

    private function releaseSlot(): void
    {
        if ($this->availability_slot_id === null) {
            return;
        }

        AvailabilitySlot::whereKey($this->availability_slot_id)
            ->update(['status' => AvailabilitySlot::STATUS_OPEN]);

        $this->update(['availability_slot_id' => null]);
    }

    protected function casts(): array
    {
        return ['appointment_date' => 'date'];
    }
}
