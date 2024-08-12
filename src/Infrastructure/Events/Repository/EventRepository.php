<?php

declare(strict_types=1);

namespace App\Infrastructure\Events\Repository;

use App\Domain\Events\Interfaces\IEventRepository;
use App\Domain\Events\Models\Event;
use App\Domain\Events\Models\EventDetails;
use App\Domain\General\Interfaces\IConnectionFactory;
use DateTimeImmutable;
use PDO;
use PDOException;

class EventRepository implements IEventRepository
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
                    events.id, 
                    event_details.id AS details_id, 
                    events.name, 
                    events.description,
                    event_details.loaction AS location,
                    event_details.price,
                    event_details.slots,
                    event_details.pre_payment, 
                    event_details.created_at,
                    events.user_id 
                FROM 
                    `events`
                LEFT JOIN 
                    `event_details`
                ON
                    events.id = event_details.event_id
                WHERE 
                    user_id=:userId'
            );
            $pstmt->execute([':userId' => $userId]);
            $events = $pstmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
        }

        return array_map(function (array $event) {
            return new Event(
                $event['name'],
                $event['description'],
                $event['user_id'],
                (new DateTimeImmutable())->setTimestamp((int) $event['created_at']),
                (int) $event['id'],
                new EventDetails(
                    $event['location'],
                    (new DateTimeImmutable())->setTimestamp((int) $event['created_at']),
                    (bool) $event['pre_payment'],
                    (float) $event['price'],
                    (int) $event['slots'],
                    (int) $event['id'],
                    (int) $event['details_id'],
                )
            );
        }, $events);
    }

    public function getById(int $userId, int $id): Event
    {
        try {
            $pstmt = ($this->connection->create())->prepare(
                'SELECT 
                    events.id,
                    event_details.id AS details_id,  
                    events.name, 
                    events.description, 
                    events.created_at,
                    event_details.loaction AS location,
                    event_details.price,
                    event_details.slots,
                    event_details.pre_payment, 
                    event_details.created_at,
                    events.user_id 
                FROM 
                    `events`
                LEFT JOIN 
                    `event_details`
                ON
                    events.id = event_details.event_id 
                WHERE 
                    events.id=:id
                AND 
                    user_id=:userId'
            );
            $pstmt->execute(
                [
                    ':userId' => $userId,
                    ':id' => $id,
                ]
            );
            $event = $pstmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        }

        return new Event(
            $event['name'],
            $event['description'],
            $event['user_id'],
            (new DateTimeImmutable())->setTimestamp((int) $event['created_at']),
            (int) $event['id'],
            new EventDetails(
                $event['location'],
                (new DateTimeImmutable())->setTimestamp((int) $event['created_at']),
                (bool) $event['pre_payment'],
                (float) $event['price'],
                (int) $event['slots'],
                (int) $event['id'],
                (int) $event['details_id'],
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function create(Event $event): Event
    {
        try {
            $conn = $this->connection->create();
            $pstmt = $conn->prepare(
                'INSERT INTO `events` 
                    VALUES(
                        NULL,
                        :name, 
                        :description, 
                        :createdAt, 
                        :userId
                    )
                '
            );

            $pstmt->execute(
                [
                    ':name' => $event->getName(),
                    ':description' => $event->getDescription(),
                    ':createdAt' => $event->getCreatedAt()->getTimestamp(),
                    ':userId' => $event->getUserId()
                ]
            );
        } catch (PDOException $e) {
            //@todo domain specific exceptions to be thrown
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        }

        return $event->withId((int) $conn->lastInsertId());
    }

    /**
     * @inheritDoc
     */
    public function delete(
        int $userId,
        int $eventId
    ): bool {
        try {
            $pstmt = ($this->connection->create())->prepare('DELETE FROM `events` WHERE id=:id AND user_id=:userId');
            $pstmt->execute(
                [
                    ':id' => $eventId,
                    ':userId' => $userId
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
    public function update(Event $event): bool
    {
        try {
            $pstmt = ($this->connection->create())->prepare(
                'UPDATE `events`
                    SET 
                        name=:name, 
                        description=:description, 
                        created_at=:createdAt, 
                        user_id=:userId
                    WHERE id=:id'
            );

            $pstmt->execute(
                [
                    ':name' => $event->getName(),
                    ':description' => $event->getDescription(),
                    ':createdAt' => $event->getCreatedAt()->getTimestamp(),
                    ':userId' => $event->getUserId(),
                    ':id' => $event->getId(),
                ]
            );
        } catch (PDOException $e) {
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        }
        //@todo return instance of event.
        return true;
    }
}
