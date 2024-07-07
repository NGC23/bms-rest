<?php 

declare(strict_types=1);

namespace Test\Infrastructure\Bookings\Repository;

use PDO;
use PDOException;
use PDOStatement;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Domain\Booking\Model\Booking;
use App\Domain\General\Models\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\General\Factory\PDOConnectionFactory;
use App\Domain\General\Interfaces\IConnectionFactory;
use App\Infrastructure\Bookings\Repository\BookingRepository;

#[CoversClass(Booking::class)]
#[CoversClass(PDOConnectionFactory::class)]
#[CoversClass(Connection::class)]
#[CoversClass(BookingRepository::class)]
class BookingRepositoryTest extends TestCase
{
    protected IConnectionFactory $connection;
    protected BookingRepository $repository;
    protected string $userId;

    public function setUp(): void
    {
        $this->connection = $this->getMockBuilder(IConnectionFactory::class)->disableOriginalConstructor()->getMock();
    }

    public function testGetAllWillThrowExceptionOnPDOException(): void
    {
        $this->expectException(PDOException::class);

        $this->connection->method('create')->willThrowException(new PDOException());

        $repository = new BookingRepository($this->connection);
        //action
        $repository->getAll(1, 1);
    }

    public function testGetAll(): void
    {
        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $statementMock = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('fetchAll')->willReturn(
            [
                ["event_id" => 1,"user_id" => 1,"start_at" => '2024-08-11 12:45',"end_at" => '2024-08-11 13:45',"id" => 1],
                ["event_id" => 1,"user_id" => 1,"start_at" => '2024-09-11 12:45',"end_at" => '2024-09-11 13:45',"id" => 2],
                ["event_id" => 1,"user_id" => 1,"start_at" => '2024-10-11 12:45',"end_at" => '2024-10-11 13:45',"id" => 3],
            ]
        );

        $pdo->method('prepare')->willReturn($statementMock);

        $this->connection->method('create')->willReturn($pdo);

        $repository = new BookingRepository($this->connection);
        $result = $repository->getAll(1, 1);

        $this->assertCount(3, $result);
        $this->assertInstanceOf(Booking::class, $result[0]);
    }

    public function testUpdateWillThrowExceptionOnPDOException(): void
    {
        $this->expectException(PDOException::class);

        $booking =  new Booking(
            1,
            1,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1
        );

        $this->connection->method('create')->willThrowException(new PDOException());

        $repository = new BookingRepository($this->connection);
        //action
        $repository->update($booking);
    }

    public function testUpdateBooking(): void
    {
        $booking =  new Booking(
            1,
            1,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1
        );

        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $statementMock = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $statementMock->method('execute')->willReturn(true);
        $pdo->method('prepare')->willReturn($statementMock);

        $this->connection->method('create')->willReturn($pdo);

        $repository = new BookingRepository($this->connection);
        $result = $repository->update($booking);

        //@todo Make this test a bit better because what are we really testing here except that the update executes..
        $this->assertEquals($booking, $result);
    }

    public function testDeleteWillThrowExceptionOnPDOException(): void
    {
        $this->expectException(PDOException::class);

        $booking =  new Booking(
            1,
            1,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1
        );

        $this->connection->method('create')->willThrowException(new PDOException());

        $repository = new BookingRepository($this->connection);
        //action
        $repository->delete(
            $booking->getUserId(),
            $booking->getId()
        );
    }

    public function testDeleteBooking(): void
    {
        $booking =  new Booking(
            1,
            1,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1
        );

        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $statementMock = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $statementMock->method('execute')->willReturn(true);
        $pdo->method('prepare')->willReturn($statementMock);
        $pdo->method('lastInsertId')->willReturn('1');

        $this->connection->method('create')->willReturn($pdo);

        $repository = new BookingRepository($this->connection);
        $result = $repository->delete($booking->getUserId(), $booking->getId());

        $this->assertTrue($result);
    }

    public function testCreateWillThrowExceptionOnPDOException(): void
    {
        $this->expectException(PDOException::class);

        $booking =  new Booking(
            1,
            1,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->connection->method('create')->willThrowException(new PDOException());

        $repository = new BookingRepository($this->connection);
        //action
        $repository->create($booking);
    }

    public function testCreateBooking(): void
    {
        $booking =  new Booking(
            1,
            1,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $statementMock = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $statementMock->method('execute')->willReturn(true);
        $pdo->method('prepare')->willReturn($statementMock);
        $pdo->method('lastInsertId')->willReturn('1');

        $this->connection->method('create')->willReturn($pdo);

        $repository = new BookingRepository($this->connection);
        $result = $repository->create($booking);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals($booking->getEventId(), $result->getEventId());
    }

    public function testGetById(): void
    {
        //arrange
        $booking = [
            'event_id' => 1,
            'user_id' => 1,
            'start_at' => '1712340841',
            'end_at' => '1712340841',
            'id' => 1
        ];

        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $statementMock = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('fetchAll')->willReturn([$booking]);
        $pdo->method('prepare')->willReturn($statementMock);

        $this->connection->method('create')->willReturn($pdo);

        $repository = new BookingRepository($this->connection);
        //action
        $results = $repository->getById(
            $booking['user_id'],
            $booking['event_id'],
            $booking['id']
        );

        //assert
        $this->assertEquals($booking['event_id'], $results->getEventId());
        $this->assertEquals($booking['user_id'], $results->getUserId());
        $this->assertEquals($booking['id'], $results->getId());
    }

    public function testGetByIdWillThrowExceptionOnPDOException(): void
    {
        $this->expectException(PDOException::class);

        $this->connection->method('create')->willThrowException(new PDOException());

        $repository = new BookingRepository($this->connection);
        //action
        $repository->getById(1, 1, 1);
    }
}
