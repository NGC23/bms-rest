<?php 

declare(strict_types=1);

namespace Test\Infrastructure\Events\Repository;

use PDO;
use PDOException;
use PDOStatement;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Domain\Events\Models\Event;
use App\Domain\General\Factory\PDOConnectionFactory;
use App\Domain\General\Models\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\General\Interfaces\IConnectionFactory;
use App\Infrastructure\Events\Repository\EventRepository;

#[CoversClass(Event::class)]
#[CoversClass(PDOConnectionFactory::class)]
#[CoversClass(Connection::class)]
#[CoversClass(EventRepository::class)]
class EventRepositoryTest extends TestCase
{
    protected IConnectionFactory $connection;
    protected EventRepository $repository;
    protected string $userId;

    public function setUp(): void
    {
        $this->connection = $this->getMockBuilder(IConnectionFactory::class)->disableOriginalConstructor()->getMock();
    }

    public function testGetAllThrowsException(): void
    {
        $this->expectException(PDOException::class);

        $this->connection->method('create')->willThrowException(new PDOException());

        $repository = new EventRepository($this->connection);

        $repository->getAll(1);
    }

    public function testGetAllEvents(): void
    {
        //arrange
        $events = [
            [
                'name' => 'test-event-1',
                'description' => 'test-description-1',
                'user_id' => 1,
                'created_at' => '1712340841',
                'id' => 1
            ],
            [
                'name' => 'test-event-2',
                'description' => 'test-description-2',
                'user_id' => 1,
                'created_at' => '1712340841',
                'id' => 2
            ],
            [
                'name' => 'test-event-3',
                'description' => 'test-description-3',
                'user_id' => 1,
                'created_at' => '1712340841',
                'id' => 3
            ],
        ];

        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $statementMock = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('fetchAll')->willReturn($events);
        $pdo->method('prepare')->willReturn($statementMock);

        $this->connection->method('create')->willReturn($pdo);

        $repository = new EventRepository($this->connection);
        //action
        $results = $repository->getAll(1);
        //assert
        $this->assertEquals(3, count($results));
        $this->assertEquals($events[0]['name'], $results[0]->getName());
        $this->assertEquals($events[1]['name'], $results[1]->getName());
        $this->assertEquals($events[2]['name'], $results[2]->getName());
    }

    public function testEventById(): void
    {
        //arrange
        $event = [
            'name' => 'test-event-1',
            'description' => 'test-description-1',
            'user_id' => 1,
            'created_at' => '1712340841',
            'id' => 1
        ];

        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $statementMock = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('fetchAll')->willReturn([$event]);
        $pdo->method('prepare')->willReturn($statementMock);

        $this->connection->method('create')->willReturn($pdo);

        $repository = new EventRepository($this->connection);
        //action
        $results = $repository->getById(1, 1);
        //assert
        $this->assertEquals($event['name'], $results->getName());
    }

    public function testEventByIdThrowsExceptionOnPDOError(): void
    {
        $this->expectException(PDOException::class);

        $this->connection->method('create')->willThrowException(new PDOException());

        $repository = new EventRepository($this->connection);
        //action
        $repository->getById(1, 1);
    }

    public function testCreateEvent(): void
    {
        $eventToCreate =  new Event(
            'test-event-1',
            'test-description-1',
            1,
            new DateTimeImmutable()
        );

        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $statementMock = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $statementMock->method('execute')->willReturn(true);
        $pdo->method('prepare')->willReturn($statementMock);
        $pdo->method('lastInsertId')->willReturn('1');

        $this->connection->method('create')->willReturn($pdo);

        $repository = new EventRepository($this->connection);
        $result = $repository->create($eventToCreate);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals($eventToCreate->getName(), $result->getName());
    }

    public function testCreateThrowsException(): void
    {
        $eventToCreate =  new Event(
            'test-event-1',
            'test-description-1',
            1,
            new DateTimeImmutable()
        );

        $this->expectException(PDOException::class);

        $this->connection->method('create')->willThrowException(new PDOException());

        $repository = new EventRepository($this->connection);

        $repository->create($eventToCreate);
    }

    public function testUpdateEvent(): void
    {
        $eventToUpdate =  new Event(
            'test-event-1',
            'test-description-1',
            1,
            new DateTimeImmutable(),
            1
        );

        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $statementMock = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $statementMock->method('execute')->willReturn(true);
        $pdo->method('prepare')->willReturn($statementMock);

        $this->connection->method('create')->willReturn($pdo);

        $repository = new EventRepository($this->connection);
        $result = $repository->update($eventToUpdate);

        $this->assertTrue($result);
    }

    public function testUpdateThrowsException(): void
    {
        $eventToUpdate =  new Event(
            'test-event-1',
            'test-description-1',
            1,
            new DateTimeImmutable(),
            1
        );

        $this->expectException(PDOException::class);

        $this->connection->method('create')->willThrowException(new PDOException());

        $repository = new EventRepository($this->connection);

        $repository->update($eventToUpdate);
    }

    public function testDeleteEvent(): void
    {
        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $statementMock = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $statementMock->method('execute')->willReturn(true);
        $pdo->method('prepare')->willReturn($statementMock);

        $this->connection->method('create')->willReturn($pdo);

        $repository = new EventRepository($this->connection);
        //action
        $result = $repository->delete(1, 1);

        $this->assertTrue($result);
    }

    public function testDeleteEventThrowsException(): void
    {
        $this->expectException(PDOException::class);

        $this->connection->method('create')->willThrowException(new PDOException());

        $repository = new EventRepository($this->connection);
        //action
        $repository->delete(1, 1);
    }

}