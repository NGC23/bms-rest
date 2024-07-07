<?php 

declare(strict_types=1);

namespace Test\Application\Events;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Domain\Events\Models\Event;
use Psr\Http\Message\RequestInterface;
use App\Application\Events\EventController;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Events\Interfaces\IEventRepository;
use PDOException;

#[CoversClass(EventController::class)]
#[CoversClass(Event::class)]
class EventControllerTest extends TestCase
{
    protected RequestInterface|MockObject $request;
    protected IEventRepository|MockObject $repository;

    public function setUp(): void
    {
        $this->request = $this->getMockBuilder(ServerRequestInterface::class)->disableOriginalConstructor()->getMock();
        $this->repository = $this->getMockBuilder(IEventRepository::class)->disableOriginalConstructor()->getMock();
    }
    
    public function testGetAllReturnsEvents(): void
    {
        $userId = 1;
        $event = new Event(
            "test-controller", 
            "test-description",
            $userId, 
            new DateTimeImmutable(),
            1
        );

        $this->request->method("getAttribute")->willReturn($userId);

        $this->repository->method('getAll')->willReturn([$event]);

        $eventController = new EventController($this->repository);
        $result = $eventController->getAll($this->request);

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testGetByIdReturnsEvent(): void
    {
        $userId = 1;
        $id = 1;
        $event = new Event(
            "test-controller", 
            "test-description",
            $userId, 
            new DateTimeImmutable(),
            $id
        );

        $this->request->method("getAttribute")->willReturn($userId);
        $this->request->method("getAttribute")->willReturn($id);

        $this->repository->method('getById')->willReturn($event);

        $eventController = new EventController($this->repository);
        $result = $eventController->getById($this->request);

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testGetByIdReturns500(): void
    {
        $userId = 1;
        $id = 1;
        $event = new Event(
            "test-controller", 
            "test-description",
            $userId, 
            new DateTimeImmutable(),
            $id
        );

        $this->request->method("getAttribute")->willReturn($userId);
        $this->request->method("getAttribute")->willReturn($id);

        $this->repository->method('getById')->willThrowException(new PDOException());

        $eventController = new EventController($this->repository);
        $result = $eventController->getById($this->request);

        $this->assertEquals(500, $result->getStatusCode());
    }

    public function testCreateEvent(): void
    {
        $event = new Event(
            'test-controller', 
            'test-description',
            1, 
            new DateTimeImmutable(),
        );

        $this->request->method('getBody')->willReturn('{"name":"test-controller","description":"test-description","user_id":1}');

        $this->repository->method('create')->willReturn(new Event(
            'test-controller', 
            'test-description',
            1,
            new DateTimeImmutable(),
            1
        ));

        $eventController = new EventController($this->repository);
        $result = $eventController->create($this->request);

        $this->assertEquals(201, $result->getStatusCode());
    }

    public function testCreateEventReturns500OnException(): void
    {
        $this->request->method('getBody')->willReturn('{"name":"test-controller","description":"test-description","user_id":"1"}');

        $this->repository->method('create')->willThrowException(new PDOException());

        $eventController = new EventController($this->repository);
        $result = $eventController->create($this->request);

        $this->assertEquals(500, $result->getStatusCode());
    }

    public function testGetAllReturns500OnException(): void
    {
        $this->request->method('getAttribute')->willReturn('some-id');

        $this->repository->method('getAll')->willThrowException(new PDOException());

        $eventController = new EventController($this->repository);
        $result = $eventController->getAll($this->request);

        $this->assertEquals(500, $result->getStatusCode());
    }

    public function testDeleteEvent(): void
    {
        $this->request->method('getAttribute')->willReturn('some-id');
        $this->request->method('getAttribute')->willReturn('1');

        $this->repository->method('delete')->willReturn(true);

        $eventController = new EventController($this->repository);
        $result = $eventController->delete($this->request);

        $this->assertEquals(204, $result->getStatusCode());
    }

    public function testDeleteEventReturns500OnException(): void
    {
        $this->request->method('getAttribute')->willReturn('some-id');
        $this->request->method('getAttribute')->willReturn('1');

        $this->repository->method('delete')->willThrowException(new PDOException());

        $eventController = new EventController($this->repository);
        $result = $eventController->delete($this->request);

        $this->assertEquals(500, $result->getStatusCode());
    }

    public function testUpdateEvent(): void
    {
        $this->request->method('getBody')->willReturn('{"name":"test-controller","description":"test-description","user_id":1,"id":"1"}');

        $this->repository->method('update')->willReturn(true);

        $eventController = new EventController($this->repository);
        $result = $eventController->update($this->request);

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testUpdateEventReturns500OnException(): void
    {
        $this->request->method('getBody')->willReturn('{"name":"test-controller","description":"test-description","user_id":1,"id":"1"}');

        $this->repository->method('update')->willThrowException(new PDOException());

        $eventController = new EventController($this->repository);
        $result = $eventController->update($this->request);

        $this->assertEquals(500, $result->getStatusCode());
    }
}