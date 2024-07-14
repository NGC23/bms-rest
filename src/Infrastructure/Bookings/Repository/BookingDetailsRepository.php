<?php

declare(strict_types=1);

namespace App\Infrastructure\Bookings\Repository;

use PDOException;
use App\Domain\General\Interfaces\IConnectionFactory;
use App\Domain\Booking\Interfaces\IBookingDetailsRepository;
use App\Domain\Booking\Model\BookerDetails;

class BookingDetailsRepository implements IBookingDetailsRepository
{
    public function __construct(private IConnectionFactory $connection)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(BookerDetails $bookerDetails): BookerDetails
    {
        try {
            $conn = $this->connection->create();
            $pstmt = $conn->prepare(
                'INSERT INTO `booking_details` 
                    VALUES(
                        NULL,
                        :first_name,
                        :last_name,
                        :email,
                        :cell_number,
                        :booking_id,
                        :created_at
                    )
                '
            );

            $pstmt->execute(
                [
                    ':first_name' => $bookerDetails->getFirstName(),
                    ':last_name' => $bookerDetails->getLasttName(),
                    ':email' => $bookerDetails->getEmail(),
                    ':cell_number' => $bookerDetails->getcellNumber(),
                    ':booking_id' => $bookerDetails->getBookingId(),
                    ':created_at' => $bookerDetails->getCreatedAt()->getTimestamp(),
                ]
            );
        } catch (PDOException $e) {
            //@todo domain specific exceptions to be thrown
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        }

        return $bookerDetails->withId((int) $conn->lastInsertId());
    }
}
