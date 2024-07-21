<?php

declare(strict_types=1);

namespace App\Domain\Booking\Model;

use DateTimeImmutable;

class Booking
{
    public function __construct(
        private int $eventId,
        private int $userId,
        private DateTimeImmutable $startAt,
        private DateTimeImmutable $endAt,
        private ?BookerDetails $bookerDetails,
        private ?string $eventName = null,
        private ?int $id = null,
    ) {
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * Get the value of userId
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Get the value of startTime
     */
    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startAt;
    }

    /**
     * Get the value of endTime
     */
    public function getEndTime(): ?DateTimeImmutable
    {
        return $this->endAt;
    }

    public function getBookerDetails(): ?BookerDetails
    {
        return $this->bookerDetails;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    public function withId(int $id): self
    {
        $booking = clone $this;
        $booking->id = $id;

        return $booking;
    }

    public function withBookerDetails(BookerDetails $bookerDetails): self
    {
        $booking = clone $this;
        $booking->bookerDetails = $bookerDetails;

        return $booking;
    }

    public function withEventName(string $name): self
    {
        $booking = clone $this;
        $booking->eventName = $name;

        return $booking;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId() ?? null,
            'eventId' => $this->getEventId(),
            'eventName' => $this->getEventName() ?? '',
            'startTime' => $this->getStartTime()->format('Y-m-d H:s:i'),
            'endTime' =>  $this->getEndTime()->format('Y-m-d H:s:i'),
            'userId' => $this->getUserId(),
            'bookerDetails' => $this->getBookerDetails()->toArray(),
        ];
    }
}
