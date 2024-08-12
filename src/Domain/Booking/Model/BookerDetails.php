<?php

declare(strict_types=1);

namespace App\Domain\Booking\Model;

use DateTimeImmutable;

class BookerDetails
{
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $cellNumber,
        private string $email,
        private DateTimeImmutable $createdAt,
        private int $bookerId,
        private ?int $bookingId = null,
        private ?int $id = null
    ) {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLasttName(): string
    {
        return $this->lastName;
    }

    public function getcellNumber(): string
    {
        return $this->cellNumber;
    }

    public function getEmail(): string
    {
        return $this->email;
    }


    public function getBookingId(): ?int
    {
        return $this->bookingId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookerId(): int
    {
        return $this->bookerId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function withId(int $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withBookingId(int $id): self
    {
        $clone = clone $this;
        $clone->bookingId = $id;
        return $clone;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'cellNumber' => $this->cellNumber,
            'email' => $this->email,
            'createdAt' => $this->createdAt->format('Y-m-d H:s:i'),
            'bookingId' => $this->bookingId,
            'bookerId' => $this->bookerId,
        ];
    }
}
