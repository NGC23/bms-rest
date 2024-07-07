<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\User\Exceptions\IncorrectPasswordException;
use App\Domain\User\Interfaces\IAuthenticationRepository;
use Throwable;
use DateTimeImmutable;
use OpenApi\Attributes as OA;
use App\Domain\User\Models\User;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class UserLoginController
{
    public function __construct(private IAuthenticationRepository $iAuthenticationRepository)
    {
    }

    #[OA\Post(path: '/user/login', tags: ["User"])]
    #[OA\Response(response: '500', description: 'Internal server error')]
    #[OA\Response(response: '200', description: 'User object')]
    public function login(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode((string) $request->getBody());

        try {
            $user = $this->iAuthenticationRepository->login(
                new User(
                    $body->email,
                    $body->password,
                    new DateTimeImmutable(),
                )
            );
        } catch (IncorrectPasswordException $e) {
            return new JsonResponse(
                [
                    'message' => $e->getMessage()
                ],
                403
            );
        } catch (Throwable $e) {
            //loggers!!!!
            return new JsonResponse(
                [
                    'message' => $e->getMessage()
                ],
                500
            );
        }

        return new JsonResponse(
            $user->toArray(),
            200
        );
    }
}
