<?php

declare(strict_types=1);

namespace App\Bms\Application\Booking;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BookingController
{
    public function __construct()
    {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        die("asdasdasdasddas");
    }
}
