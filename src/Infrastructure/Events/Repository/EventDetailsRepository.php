<?php

declare(strict_types=1);

namespace App\Infrastructure\Events\Repository;

use PDOException;
use App\Domain\Events\Models\EventDetails;
use App\Domain\General\Interfaces\IConnectionFactory;
use App\Domain\Events\Interfaces\IEventDetailsRepository;
use Throwable;

class EventDetailsRepository implements IEventDetailsRepository
{
    public function __construct(private IConnectionFactory $connection)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(EventDetails $details): EventDetails
    {
        try {
            $conn = $this->connection->create();
            $pstmt = $conn->prepare(
                'INSERT INTO `event_details` 
                    VALUES(
                        NULL,
                        :loc, 
                        :slots, 
                        :price, 
                        :event_id,
                        :pre_payment, 
                        :created_at
                    )
                '
            );
 
            $pstmt->execute(
                [
                    ':loc' => $details->getLocation(),
                    ':slots' => $details->getSlots(),
                    ':price' => $details->getPrice(),
                    ':pre_payment' => (int) $details->getPrePayment(),
                    ':event_id' => $details->getEventId(),
                    ':created_at' => $details->getCreatedAt()->getTimestamp(),
                ]
            );
        } catch (PDOException $e) {
            //@todo domain specific exceptions to be thrown
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        } catch (Throwable $e) {
            throw $e;
        }

        return $details->withId((int) $conn->lastInsertId());
    }

    /**
     * @inheritDoc
     */
    public function delete(
        int $id,
        int $eventId
    ): bool {
        try {
            $pstmt = ($this->connection->create())->prepare('DELETE FROM `event_details` WHERE id=:id AND event_id=:event_id');
            $pstmt->execute(
                [
                    ':id' => $id,
                    ':event_id' => $eventId
                ]
            );
        } catch (PDOException $e) {
            throw $e;
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
        } catch (Throwable $e) {
            throw $e;
        }

        return true;
    }
}
