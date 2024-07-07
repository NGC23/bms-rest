<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Repository;

use PDO;
use Exception;
use PDOException;
use App\Domain\User\Models\User;
use App\Domain\General\Interfaces\IConnectionFactory;
use App\Domain\User\Interfaces\IAuthenticationRepository;
use App\Domain\User\Exceptions\IncorrectPasswordException;
use DateTimeImmutable;

class AuthenticationRepository implements IAuthenticationRepository
{
    public function __construct(private IConnectionFactory $connection)
    {
    }

    /**
     * @inheritDoc
     */
    public function login(User $user): User
    {
        try {
            $conn = $this->connection->create();

            $pstmt = $conn->prepare(
                'SELECT 
                    id,
                    email,
                    password,
                    created_at
                FROM
                    users
                WHERE
                    email = :email
                '
            );

            $pstmt->execute([
                ':email' =>  $user->getEmail()
            ]);

            $result = $pstmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            //@todo domain specific exceptions to be thrown
            //log and throw domain exception that we are not coupled to the PDO exceptions.
            //Catch in presentation layer and return approapriate status
            throw $e;
        }

        if (!password_verify($user->getPassword(), $result['password'])) {
            throw new IncorrectPasswordException("Incorrect password supplied with email: {$user->getEmail()}");
        }

        return new User(
            $result['email'],
            $result['password'],
            (new DateTimeImmutable())->setTimestamp((int) $result['created_at']),
            (int) $result['id'],
        );
    }
}
