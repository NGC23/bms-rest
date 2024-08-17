<?php 

declare(strict_types=1);

namespace Test\Application\Events;

use App\Application\Booking\BookingController;
use App\Domain\Booking\Interfaces\IBookingDetailsRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use App\Domain\Booking\Interfaces\IBookingRepository;
use App\Domain\Booking\Model\BookerDetails;
use App\Domain\Booking\Model\Booking;
use App\Domain\Booking\Model\BookingDetails;
use App\Domain\Events\Interfaces\IEventRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PDOException;

#[CoversClass(BookingController::class)]
#[CoversClass(BookingDetails::class)]
#[CoversClass(Booking::class)]
#[CoversClass(BookerDetails::class)]
class BookingControllerTest extends TestCase
{
    protected RequestInterface|MockObject $request;
    protected IBookingRepository|MockObject $repository;
    protected IBookingDetailsRepository|MockObject $bookingDeailsrepository;
    protected IEventRepository|MockObject $eventRepository;

    public function setUp(): void
    {
        $this->request = $this->getMockBuilder(ServerRequestInterface::class)->disableOriginalConstructor()->getMock();
        $this->repository = $this->getMockBuilder(IBookingRepository::class)->disableOriginalConstructor()->getMock();
        $this->bookingDeailsrepository = $this->getMockBuilder(IBookingDetailsRepository::class)->disableOriginalConstructor()->getMock();
        $this->eventRepository = $this->getMockBuilder(IEventRepository::class)->disableOriginalConstructor()->getMock();
    }

    public function testCreateBooking(): void
    {
        $booking = new Booking(
            1,
            1,
            new DateTimeImmutable("2024-08-11 12:45"),
            new DateTimeImmutable("2024-08-11 13:45"),
            null,
            new BookerDetails(
                'test',
                'test-surname',
                '0614430444',
                'test@gmail.com',
                new DateTimeImmutable(),
                1
            ),
            "test-event",
            1
        );

        $this->request->method('getBody')->willReturn('{"eventId":1,"userId":1,"startTime":"2024-08-11 12:45","endTime": "2024-08-11 13:45","bookerDetails": {"firstName": "test","lastName": "test-surname","cellNumber":"0614430444","email":"test@gmail.com", "bookerId":1}}');

        $this->repository->method('create')->willReturn($booking);

        $this->bookingDeailsrepository->method('create')->willReturn($booking->getBookerDetails()->withBookingId($booking->getId()));

        $bookingController = new BookingController(
            $this->repository,
            $this->bookingDeailsrepository,
            $this->eventRepository
        );
        $result = $bookingController->create($this->request);

        $this->assertEquals(201, $result->getStatusCode());
    }

    public function testCreateEventReturns500OnException(): void
    {
        $this->request->method('getBody')->willReturn('{"eventId":1,"userId":1,"startTime":"2024-08-11 12:45","endTime": "2024-08-11 13:45","bookerDetails": {"firstName": "test","lastName": "test-surname","cellNumber":"0614430444","email":"test@gmail.com"}}');

        $this->repository->method('create')->willThrowException(new PDOException());

        $bookingController = new BookingController(
            $this->repository,
            $this->bookingDeailsrepository,
            $this->eventRepository
        );
        $result = $bookingController->create($this->request);

        $this->assertEquals(500, $result->getStatusCode());
    }
}