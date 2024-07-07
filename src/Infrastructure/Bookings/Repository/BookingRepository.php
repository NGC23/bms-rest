<?php

declare(strict_types=1);

namespace App\Infrastructure\Bookings\Repository;

use PDO;
use PDOException;
use DateTimeImmutable;
use App\Domain\Booking\Model\Booking;
use App\Domain\General\Interfaces\IConnectionFactory;
use App\Domain\Booking\Interfaces\IBookingRepository;

class BookingRepository implements IBookingRepository
{
    public function __construct(private IConnectionFactory $connection)
    {
    }

    /**
     * @inheritDoc
     */
    public function getAll(int $userId, int $eventId): array
    {
        try {
            $pstmt = ($this->connection->create())->prepare(
                'SELECT 
                    id, 
                    event_id,
                    user_id,
                    start_at,
                    end_at,
                FROM 
                    `bookings` 
                WHERE 
                    user_id=:userId
                AND
                    event_id=:eventId'
            );
            $pstmt->execute(
                [
                    ':userId' => $userId,
                    ':eventId' => $eventId
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
                (int) $booking['id']
            );
        }, $bookings);
    }

    /**
     * @inheritDoc
     */
    public function getById(
        int $userId,
        int $eventId,
        int $id
    ): Booking {
        try {
            $pstmt = ($this->connection->create())->prepare(
                'SELECT 
                    id, 
                    event_id,
                    user_id,
                    start_at,
                    end_at,
                FROM 
                    `bookings` 
                WHERE 
                    id=:id
                AND 
                    user_id=:userId
                AND 
                    event_id=:eventId'
            );

            $pstmt->execute(
                [
                    ':userId' => $userId,
                    ':eventId' => $eventId,
                    ':id' => $id,
                ]
            );

            $booking = $pstmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        }

        return new Booking(
            (int) $booking[0]['event_id'],
            (int) $booking[0]['user_id'],
            (new DateTimeImmutable())->setTimestamp((int) $booking[0]['start_at']),
            (new DateTimeImmutable())->setTimestamp((int) $booking[0]['end_at']),
            (int) $booking[0]['id']
        );
    }

    /**
     * @inheritDoc
     */
    public function create(Booking $booking): Booking
    {
        try {
            $pstmt = ($this->connection->create())->prepare(
                'INSERT INTO `bookings` 
                    VALUES(
                        NULL,
                        id, 
                        event_id,
                        user_id,
                        start_at,
                        end_at,
                    )
                '
            );

            $pstmt->execute(
                [
                    ':id' => $booking->getId(),
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

        return $booking->withId((int) ($this->connection->create())->lastInsertId());
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
}
