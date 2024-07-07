<?php

declare(strict_types=1);

namespace App\Application\Booking;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BookingController
{
    public function __construct()
    {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'message' => 'welcome to the bms rest - this is a test'
        ]);
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'message' => 'welcome to the bms rest - this is a test'
        ]);
    }
}
