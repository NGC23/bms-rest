<?php

declare(strict_types=1);

namespace App\Infrastructure\Bookings\Repository;

use PDO;
use PDOException;
use DateTimeImmutable;
use App\Domain\Booking\Model\Booking;
use App\Domain\General\Interfaces\IConnectionFactory;
use App\Domain\Booking\Interfaces\IBookingRepository;
use App\Domain\Booking\Model\BookerDetails;
use Throwable;

class BookingRepository implements IBookingRepository
{
    public function __construct(private IConnectionFactory $connection)
    {
    }

    /**
     * @inheritDoc
     */
    public function getAll(int $userId): array
    {
        try {
            $pstmt = ($this->connection->create())->prepare(
                'SELECT 
                    bookings.id, 
                    bookings.event_id,
                    bookings.user_id,
                    bookings.start_at,
                    bookings.end_at,
                    events.name AS event_name,
                    booking_details.id AS details_id,
                    booking_details.booker_id AS booker_id,
                    booking_details.first_name,
                    booking_details.last_name,
                    booking_details.email,
                    booking_details.cell_number,
                    booking_details.created_at
                FROM 
                    `bookings`
                LEFT JOIN 
                    `booking_details`
                ON
                    booking_details.booking_id = bookings.id
                LEFT JOIN
                    events
                ON
                    events.id = bookings.event_id 
                WHERE 
                    bookings.user_id = :userId'
            );
            $pstmt->execute(
                [
                    ':userId' => $userId
                ]
            );
            $bookings = $pstmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
        }

        return array_map(function (array $booking) {
            return new Booking(
                (int) $booking['event_id'],
                (int) $booking['user_id'],
                (new DateTimeImmutable())->setTimestamp((int) $booking['start_at']),
                (new DateTimeImmutable())->setTimestamp((int) $booking['end_at']),
                null,
                new BookerDetails(
                    $booking['first_name'],
                    $booking['last_name'],
                    $booking['cell_number'],
                    $booking['email'],
                    (new DateTimeImmutable())->setTimestamp((int) $booking['created_at']),
                    (int) $booking['booker_id'],
                    (int) $booking['id'],
                    (int) $booking['details_id'],
                ),
                $booking['event_name'],
                (int) $booking['id']
            );
        }, $bookings);
    }

    /**
     * @inheritDoc
     */
    public function getAllByEventId(
        int $userId,
        int $eventId
    ): array {
        try {
            $pstmt = ($this->connection->create())->prepare(
                'SELECT 
                    bookings.id, 
                    bookings.event_id,
                    bookings.user_id,
                    bookings.start_at,
                    bookings.end_at,
                    events.name AS event_name,
                    booking_details.id AS details_id,
                    booking_details.booker_id AS booker_id,
                    booking_details.first_name,
                    booking_details.last_name,
                    booking_details.email,
                    booking_details.cell_number,
                    booking_details.created_at,
                FROM 
                    `bookings`
                LEFT JOIN 
                    `booking_details`
                ON
                    booking_details.booking_id = bookings.id
                LEFT JOIN
                    events
                ON
                    events.id = bookings.event_id  
                WHERE 
                    bookings.user_id=:userId
                AND 
                    bookings.event_id=:eventId'
            );

            $pstmt->execute(
                [
                    ':userId' => $userId,
                    ':eventId' => $eventId
                ]
            );

            $bookings = $pstmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        }

        return array_map(function (array $booking) {
            return new Booking(
                (int) $booking['event_id'],
                (int) $booking['user_id'],
                (new DateTimeImmutable())->setTimestamp((int) $booking['start_at']),
                (new DateTimeImmutable())->setTimestamp((int) $booking['end_at']),
                null,
                null,
                $booking['event_name'],
                (int) $booking['id']
            );
        }, $bookings);
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): Booking {
        try {
            $pstmt = ($this->connection->create())->prepare(
                'SELECT 
                    bookings.id, 
                    bookings.event_id,
                    bookings.user_id,
                    bookings.start_at,
                    bookings.end_at,
                    events.name AS event_name,
                    booking_details.id AS details_id,
                    booking_details.booker_id AS booker_id,
                    booking_details.first_name,
                    booking_details.last_name,
                    booking_details.email,
                    booking_details.cell_number,
                    booking_details.created_at
                FROM 
                    `bookings`
                LEFT JOIN 
                    `booking_details`
                ON
                    booking_details.booking_id = bookings.id
                LEFT JOIN
                    events
                ON
                    events.id = bookings.event_id   
                WHERE 
                     bookings.id=:id'
            );

            $pstmt->execute(
                [':id' => $id]
            );

            $booking = $pstmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        } catch (Throwable $e) {
            throw $e;
        }

        return new Booking(
            (int) $booking['event_id'],
            (int) $booking['user_id'],
            (new DateTimeImmutable())->setTimestamp((int) $booking['start_at']),
            (new DateTimeImmutable())->setTimestamp((int) $booking['end_at']),
            null,
            new BookerDetails(
                $booking['first_name'],
                $booking['last_name'],
                $booking['cell_number'],
                $booking['email'],
                (new DateTimeImmutable())->setTimestamp((int) $booking['created_at']),
                (int) $booking['booker_id'],
                (int) $booking['id'],
                (int) $booking['details_id'],
            ),
            $booking['event_name'],
            (int) $booking['id']
        );
    }


    /**
     * @inheritDoc
     */
    public function create(Booking $booking): Booking
    {
        try {
            $conn = $this->connection->create();
            $pstmt = $conn->prepare(
                'INSERT INTO `bookings` 
                    VALUES(
                        NULL,
                        :event_id,
                        :user_id,
                        :start_at,
                        :end_at
                    )
                '
            );

            $pstmt->execute(
                [
                    ':event_id' => $booking->getEventId(),
                    ':user_id' => $booking->getUserId(),
                    ':start_at' => $booking->getStartTime()->getTimestamp(),
                    ':end_at' => $booking->getEndTime()->getTimestamp(),
                ]
            );
        } catch (PDOException $e) {
            //@todo domain specific exceptions to be thrown
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        }

        return $booking->withId((int) $conn->lastInsertId());
    }

    /**
     * @inheritDoc
     */
    public function delete(
        int $userId,
        int $bookingId
    ): bool {
        try {
            $pstmt = ($this->connection->create())->prepare('DELETE FROM `bookings` WHERE id=:id AND user_id=:userId');
            $pstmt->execute(
                [
                    ':id' => $bookingId,
                    ':userId' => $userId,
                ]
            );
        } catch (PDOException $e) {
            throw $e;
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function update(Booking $booking): Booking
    {
        try {
            $pstmt = ($this->connection->create())->prepare(
                'UPDATE `bookings`
                    SET 
                        event_id=:eventId,
                        created_at=:createdAt, 
                        start_at=:startDate, 
                        end_at=:endDate, 
                    WHERE id=:id'
            );

            $pstmt->execute(
                [
                    ':eventId' => $booking->getEventId(),
                    ':startDate' => $booking->getStartTime()->getTimestamp(),
                    ':endDate' => $booking->getEndTime()->getTimestamp(),
                    ':id' => $booking->getId(),
                ]
            );
        } catch (PDOException $e) {
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        }
        //@todo return instance of event.
        return $booking;
    }

    /**
     * @inheritDoc
     */
    public function getAllByBookerId(int $userId): array
    {
        try {
            $pstmt = ($this->connection->create())->prepare(
                'SELECT 
                    bookings.id, 
                    bookings.event_id,
                    bookings.user_id,
                    bookings.start_at,
                    bookings.end_at,
                    events.name AS event_name,
                    booking_details.id AS details_id,
                    booking_details.booker_id AS booker_id,
                    booking_details.first_name,
                    booking_details.last_name,
                    booking_details.email,
                    booking_details.cell_number,
                    booking_details.created_at
                FROM 
                    `bookings`
                LEFT JOIN 
                    `booking_details`
                ON
                    booking_details.booking_id = bookings.id
                LEFT JOIN
                    events
                ON
                    events.id = bookings.event_id 
                WHERE 
                    booking_details.booker_id = :userId'
            );
            $pstmt->execute(
                [
                    ':userId' => $userId
                ]
            );
            $bookings = $pstmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
        }

        return array_map(function (array $booking) {
            return new Booking(
                (int) $booking['event_id'],
                (int) $booking['user_id'],
                (new DateTimeImmutable())->setTimestamp((int) $booking['start_at']),
                (new DateTimeImmutable())->setTimestamp((int) $booking['end_at']),
                null,
                new BookerDetails(
                    $booking['first_name'],
                    $booking['last_name'],
                    $booking['cell_number'],
                    $booking['email'],
                    (new DateTimeImmutable())->setTimestamp((int) $booking['created_at']),
                    (int) $booking['booker_id'],
                    (int) $booking['id'],
                    (int) $booking['details_id'],
                ),
                $booking['event_name'],
                (int) $booking['id']
            );
        }, $bookings);
    }
}
