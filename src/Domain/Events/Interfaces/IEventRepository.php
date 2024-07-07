<?php

declare(strict_types=1);

namespace App\Domain\Events\Interfaces;

use App\Domain\Events\Models\Event;

interface IEventRepository
{
    /**
     * get Events function
     *
     * @param int $userId
     * @return Event[]
     */
    public function getAll(int $userId): array;

    /**
     * get event by id
     *
     * @param int $userId
     * @param integer $id
     * @return Event
     */
    public function getById(int $userId, int $id): Event;

    /**
     * create Event function
     *
     * @param Event $event
     * @return Event
     */
    public function create(Event $event): Event;

    /**
     * update event
     *
     * @param Event $event
     * @return bool
     */
    public function update(Event $event): bool;

    /**
     * delete event
     *
     * @param int $userId
     * @param int $eventId
     * @return bool
     */
    public function delete(
        int $userId,
        int $eventId
    ): bool;
}
