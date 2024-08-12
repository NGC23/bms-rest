<?php

declare(strict_types=1);

namespace App\Domain\Booking\Model;

use DateTimeImmutable;

class BookingDetails
{
    public const BOOKING_STATUS_BOOKED = 'booked';
    public const BOOKING_STATUS_CANCELED = 'canceled';

    public function __construct(
        private string $status,
        private ?int $id = null
    ) {
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function withId(int $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
        ];
    }
}
