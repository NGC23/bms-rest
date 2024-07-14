<?php

declare(strict_types=1);

namespace App\Domain\Booking\Interfaces;

use App\Domain\Booking\Model\Booking;

interface IBookingRepository
{
    /**
     * get Booking function
     *
     * @param int $userId
     * @return Booking[]
     */
    public function getAll(int $userId): array;

    /**
     * get booking by id
     *
     * @param integer $userId
     * @param integer $eventId
     * @param integer $id
     * @return Booking
     */
    public function getById(
        int $userId,
        int $eventId,
        int $id
    ): Booking;

    /**
     * get Booking by id
     *
     * @param int $userId
     * @param int $eventId
     * @return Booking[]
     */
    public function getAllByEventId(
        int $userId,
        int $eventId,
    ): array;

    /**
     * create Booking function
     *
     * @param Booking $event
     * @return Booking
     */
    public function create(Booking $booking): Booking;

    /**
     * update Booking
     *
     * @param Booking $event
     * @return Booking
     */
    public function update(Booking $booking): Booking;

    /**
     * delete Booking
     *
     * @param int $userId
     * @param int $bookingId
     * @return bool
     */
    public function delete(
        int $userId,
        int $bookingId
    ): bool;
}
