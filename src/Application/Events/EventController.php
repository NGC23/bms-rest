<?php

declare(strict_types=1);

namespace App\Application\Events;

use App\Domain\Events\Interfaces\IEventDetailsRepository;
use App\Domain\Events\Models\EventDetails;
use Exception;
use DateTimeImmutable;
use OpenApi\Attributes as OA;
use App\Domain\Events\Models\Event;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use App\Domain\Events\Interfaces\IEventRepository;
use Throwable;

class EventController
{
    public function __construct(
        private IEventRepository $eventRepository,
        private IEventDetailsRepository $eventDetailsRepository
    ) {
    }

    #[OA\Get(path: '/events', tags: ["Events"])]
    #[OA\Response(response: '500', description: 'Internal server error')]
    #[OA\Response(response: '200', description: 'Event object')]
    public function getAll(ServerRequestInterface $request): ResponseInterface
    {
        $userId = (int) $request->getAttribute('userId');
        try {
            $events = $this->eventRepository->getAll($userId);
        } catch (Exception $e) {
            return new JsonResponse(
                [
                    'message' => "Cannot retrieve events at this moment: {$e->getMessage()}"
                ],
                500
            );
        }

        $return = array_map(function (Event $event) {
            return $event->toArray();
        }, $events);

        return new JsonResponse($return);
    }

    #[OA\Get(path: '/events/:id', tags: ["Events"])]
    #[OA\Response(response: '500', description: 'Internal server error')]
    #[OA\Response(response: '200', description: 'Event object')]
    public function getById(ServerRequestInterface $request): ResponseInterface
    {
        $userId = (int) $request->getAttribute('userId');
        $id = (int) $request->getAttribute('id');

        try {
            $event = $this->eventRepository->getById($userId, $id);
        } catch (Exception $e) {
            return new JsonResponse(
                [
                    'message' => 'something went wrong with retrieval of the event, contact support if this persists' . $e->getMessage()
                ],
                500
            );
        }

        return new JsonResponse(
            $event->toArray(),
            200
        );
    }

    #[OA\Post(path: '/events/create', tags: ["Events"])]
    #[OA\Response(response: '500', description: 'Internal server error')]
    #[OA\Response(response: '201', description: 'Event object')]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode((string) $request->getBody());

        try {
            $event = $this->eventRepository->create(
                new Event(
                    $body->name,
                    $body->description,
                    (int) $body->user_id,
                    new DateTimeImmutable(),
                    null,
                    new EventDetails(
                        $body->location,
                        new DateTimeImmutable(),
                        (bool) $body->prePayment,
                        (float) $body->price,
                        (int) $body->slots,
                    )
                )
            );
        } catch (Throwable $e) {
            return new JsonResponse(
                [
                    'message' => 'something went wrong with creation of the event, contact support if this persists' . $e->getMessage()
                ],
                500
            );
        }

        $eventDetails = $event->getEventDetails();
        $eventDetails = $eventDetails->withEventId($event->getId());
        $event = $event->withEventDetails($eventDetails);

        try {
            $this->eventDetailsRepository->create($eventDetails);
        } catch (Throwable $e) {
            return new JsonResponse(
                [
                    'message' => 'something went wrong with creation of the event details, contact support if this persists' . $e->getMessage()
                ],
                500
            );
        }

        return new JsonResponse(
            $event->toArray(),
            201
        );
    }

    #[OA\Patch(path: '/events/update', tags: ["Events"])]
    #[OA\Response(response: '500', description: 'Internal server error')]
    #[OA\Response(response: '200', description: 'Event object')]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode((string)$request->getBody());

        try {
            $this->eventRepository->update(
                new Event(
                    $body->name,
                    $body->description,
                    (int) $body->user_id,
                    new DateTimeImmutable(),
                    null,
                    new EventDetails(
                        $body->location,
                        new DateTimeImmutable(),
                        (bool) $body->prePayment,
                        (float) $body->price,
                        (int) $body->slots,
                    ),
                    (int) $body->id
                )
            );
        } catch (Throwable $e) {
            return new JsonResponse(
                [
                    'message' => 'something went wrong with updating of the event, contact support if this persists'
                ],
                500
            );
        }

        return new JsonResponse(
            [],
            200
        );
    }

    #[OA\Delete(path: '/events/:userId/:id', tags: ["Events"])]
    #[OA\Response(response: '500', description: 'Internal server error')]
    #[OA\Response(response: '200', description: 'Event object')]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $userId = (int) $request->getAttribute('userId');

        try {
            $this->eventRepository->delete($userId, $id);
        } catch (Exception) {
            return new JsonResponse(
                [
                    'message' => 'something went wrong with deletion of the event, contact support if this persists'
                ],
                500
            );
        }

        return new JsonResponse([], 204);
    }
}
