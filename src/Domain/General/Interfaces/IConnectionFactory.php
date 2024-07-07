<?php

declare(strict_types=1);

namespace App\Domain\General\Interfaces;

use PDO;

interface IConnectionFactory
{
    public function create(): PDO;
}
