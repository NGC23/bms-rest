<?php

declare(strict_types=1);

namespace App\Domain\Events\Models;

use DateTimeImmutable;

class EventDetails
{
    public function __construct(
        private string $location,
        private DateTimeImmutable $createdAt,
        private bool $prePayment,
        private float $price = 0.00,
        private int $slots = 0,
        private ?int $eventId = null,
        private ?int $id = null
    ) {
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPrePayment(): bool
    {
        return $this->prePayment;
    }

    public function getSlots(): int
    {
        return $this->slots;
    }

    public function getEventId(): ?int
    {
        return $this->eventId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function withId(int $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withEventId(int $eventId): self
    {
        $clone = clone $this;
        $clone->eventId = $eventId;
        return $clone;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'location' => $this->location,
            'price' => $this->price,
            'prePayment' => $this->prePayment,
            'slots' => $this->slots,
            'createdAt' => $this->createdAt->format('Y-m-d H:s:i'),
            'eventId' => $this->eventId,
        ];
    }
}
