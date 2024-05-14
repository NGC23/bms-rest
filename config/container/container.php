<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use App\Domain\Genral\Interfaces\IConnectionFactory;
use App\Domain\Genral\Models\Connection;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$container = new League\Container\Container();

$container->add(\App\Bms\Application\Booking\BookingController::class);
