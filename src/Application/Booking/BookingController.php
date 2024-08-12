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
use App\Domain\Booking\Model\BookingDetails;
use App\Domain\Events\Interfaces\IEventRepository;
use DateTimeImmutable;
use Throwable;

class BookingController
{
    public function __construct(
        private IBookingRepository $iBookingRepository,
        private IBookingDetailsRepository $iBookingDetailsRepository,
        private IEventRepository $iEventRepository
    ) {
    }

    public function getById(ServerRequestInterface $request): ResponseInterface
    {
        $bookingId = (int) $request->getAttribute('bookingId');

        try {
            $booking = $this->iBookingRepository->getById((int) $bookingId);
        } catch (Throwable $exception) {
            return new JsonResponse(
                [$exception->getMessage()], // need to think of how to construct global json, can be enforced in response factory...
                500
            );
        }

        return new JsonResponse($booking->toArray());
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

    public function getAllByBookerId(ServerRequestInterface $request): ResponseInterface
    {
        $bookerId = (int) $request->getAttribute('bookerId');

        try {
            $bookings = $this->iBookingRepository->getAllByBookerId($bookerId);
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
                new BookingDetails(
                    BookingDetails::BOOKING_STATUS_BOOKED
                ),
                new BookerDetails(
                    $body->bookerDetails->firstName,
                    $body->bookerDetails->lastName,
                    $body->bookerDetails->cellNumber,
                    $body->bookerDetails->email,
                    new DateTimeImmutable(),
                    (int) $body->bookerDetails->bookerId,
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
