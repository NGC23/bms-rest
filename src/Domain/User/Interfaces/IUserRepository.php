<?php

declare(strict_types=1);

namespace App\Domain\User\Interfaces;

use App\Domain\User\Models\User;

interface IUserRepository
{
    /**
     * create function
     *
     * @param User $user
     * @return User
     */
    public function create(User $user): User;
}