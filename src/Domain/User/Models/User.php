<?php

declare(strict_types=1);

namespace App\Domain\User\Models;

use DateTimeImmutable;

class User
{
    public const USER_TYPE_BOOKER = 'booker';
    public const USER_TYPE_USER = 'user';

    public function __construct(
        private string $email,
        private string $password,
        private DateTimeImmutable $createdAt,
        private string $type = self::USER_TYPE_USER,
        private ?int $id = null
    ) {
    }

    /**
     * Get the value of email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function withId(int $id): self
    {
        $user = clone $this;
        $user->id = $id;

        return $user;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId() ?? null,
            'email' => $this->getEmail(),
            'type' => $this->getType(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:s:i')
        ];
    }
}
