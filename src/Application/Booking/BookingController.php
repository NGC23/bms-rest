<?php

declare(strict_types=1);

namespace App\Application\Booking;

use App\Domain\Booking\Interfaces\IBookingDetailsRepository;
use Exception;
use App\Domain\Booking\Model\Booking;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use App\Domain\Booking\Interfaces\IBookingRepository;
use App\Domain\Booking\Model\BookerDetails;
use DateTimeImmutable;
use Throwable;
use TypeError;

class BookingController
{
    public function __construct(
        private IBookingRepository $iBookingRepository,
        private IBookingDetailsRepository $iBookingDetailsRepository
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([]);
    }

    public function getAll(ServerRequestInterface $request): ResponseInterface
    {
        $userId = (int) $request->getAttribute('userId');
        
        try {
            $bookings = $this->iBookingRepository->getAll($userId);
        } catch (Throwable $exception) {
            return new JsonResponse(
                [$exception->getMessage()], // need to think of how to construct global json, can be enforced in response factory...
                500
            );
        }

        $bookings = array_map(function (Booking $booking) {
            return $booking->toArray();
        }, $bookings);

        return new JsonResponse($bookings);
    }

    public function getAllForEventId(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([]);
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode((string) $request->getBody());
        //need validation
        try {
            $booking = new Booking(
                (int) $body->eventId,
                (int) $body->userId,
                new DateTimeImmutable($body->startTime),
                new DateTimeImmutable($body->endTime),
                new BookerDetails(
                    $body->bookerDetails->firstName,
                    $body->bookerDetails->lastName,
                    $body->bookerDetails->cellNumber,
                    $body->bookerDetails->email,
                    new DateTimeImmutable()
                )
            );

            $booking = $this->iBookingRepository->create($booking);
        } catch (Exception $e) {
            return new JsonResponse(
                [$e->getMessage()], // need to think of how to construct global json, can be enforced in response factory...
                500
            );
        }

        //After main booking entry is created create the child enrty in booking details table
        try {
            $bookerDetails = $this->iBookingDetailsRepository->create(
                $booking->getBookerDetails()->withBookingId(
                    $booking->getId()
                )
            );
        } catch (Throwable $e) {
            return new JsonResponse(
                [$e->getMessage()], // need to think of how to construct global json, can be enforced in response factory...
                500
            );
        }

        $booking = $booking->withBookerDetails($bookerDetails);

        return new JsonResponse(
            $booking->toArray(),
            201
        );
    }
}
