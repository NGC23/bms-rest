<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Repository;

use PDOException;
use App\Domain\User\Models\User;
use App\Domain\User\Interfaces\IUserRepository;
use App\Domain\General\Interfaces\IConnectionFactory;

class UserRepository implements IUserRepository
{
    public function __construct(private IConnectionFactory $connection)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(User $user): User
    {
        try {
            $conn = $this->connection->create();

            $pstmt = $conn->prepare(
                'INSERT INTO `users` 
                    VALUES(
                        NULL,
                        :email, 
                        :password, 
                        :createdAt,
                        :type
                    )
                '
            );

            $pstmt->execute(
                [
                    ':email' => $user->getEmail(),
                    ':type' => $user->getType(),
                    ':password' => $user->getPassword(),
                    ':createdAt' => $user->getCreatedAt()->getTimestamp(),
                ]
            );
        } catch (PDOException $e) {
            //@todo domain specific exceptions to be thrown
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        }

        return $user->withId((int) $conn->lastInsertId());
    }
}
