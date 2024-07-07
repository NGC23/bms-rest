<?php

declare(strict_types=1);

namespace App\Domain\User\Interfaces;

use App\Domain\User\Models\User;

interface IAuthenticationRepository
{
    /**
     * Undocumented function
     *
     * @param User $user
     * @return User
     */
    public function login(User $user): User;
}
