<?php

declare(strict_types=1);

namespace App\Application\User;

use Throwable;
use DateTimeImmutable;
use OpenApi\Attributes as OA;
use App\Domain\User\Models\User;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use App\Domain\User\Interfaces\IUserRepository;

class UserRegistrationController
{
    public function __construct(private IUserRepository $iUserRepository)
    {
    }

    #[OA\Post(path: '/user/register', tags: ["User"])]
    #[OA\Response(response: '500', description: 'Internal server error')]
    #[OA\Response(response: '201', description: 'User object')]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode((string) $request->getBody());

        try {
            $user = $this->iUserRepository->create(
                new User(
                    $body->email,
                    password_hash($body->password, PASSWORD_DEFAULT),
                    new DateTimeImmutable(),
                )
            );
        } catch (Throwable $e) {
            //loggers!!!!
            return new JsonResponse(
                [
                    'message' => 'Cannot create user at this time!'
                ],
                500
            );
        }

        return new JsonResponse(
            $user->toArray(),
            201
        );
    }
}
