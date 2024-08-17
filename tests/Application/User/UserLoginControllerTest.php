<?php 

declare(strict_types=1);

namespace Test\Application\User;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use App\Application\User\UserLoginController;
use App\Domain\User\Exceptions\IncorrectPasswordException;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\User\Interfaces\IAuthenticationRepository;
use App\Domain\User\Models\User;
use DateTimeImmutable;
use Exception;

#[CoversClass(UserLoginController::class)]
#[CoversClass(User::class)]
class UserLoginControllerTest extends TestCase
{
    protected RequestInterface|MockObject $request;
    protected IAuthenticationRepository|MockObject $repository;

    public function setUp(): void
    {
        $this->request = $this->getMockBuilder(ServerRequestInterface::class)->disableOriginalConstructor()->getMock();
        $this->repository = $this->getMockBuilder(IAuthenticationRepository::class)->disableOriginalConstructor()->getMock();
    }

    public function testLoginReturns403OnIncorrectPassword(): void
    {
        $this->request->method('getBody')->willReturn('{"email":"test-email@test.com","password":"test-password"}');

        $this->repository->method('login')->willThrowException(new IncorrectPasswordException());

        $loginController = new UserLoginController($this->repository);
        $result = $loginController->login($this->request);

        $this->assertEquals(403, $result->getStatusCode());
    }

    public function testLoginReturns500OnGenralException(): void
    {
        $this->request->method('getBody')->willReturn('{"email":"test-email@test.com","password":"test-password"}');

        $this->repository->method('login')->willThrowException(new Exception());

        $loginController = new UserLoginController($this->repository);
        $result = $loginController->login($this->request);

        $this->assertEquals(500, $result->getStatusCode());
    }

    public function testLoginReturns200(): void
    {
        $this->request->method('getBody')->willReturn('{"email":"test-email@test.com","password":"test-password"}');

        $this->repository->method('login')->willReturn(
            new User(
                'test-email@test.com',
                'blaaaah',
                new DateTimeImmutable(),
                User::USER_TYPE_USER,
                1
            )
        );

        $loginController = new UserLoginController($this->repository);
        $result = $loginController->login($this->request);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('test-email@test.com', json_decode((string) $result->getBody())->email);
    }
}
