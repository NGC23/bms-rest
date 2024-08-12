<?php

declare(strict_types=1);

namespace App\Domain\Events\Interfaces;

use App\Domain\Events\Models\EventDetails;

interface IEventDetailsRepository
{
    /**
     * create Event details function
     *
     * @param EventDetails $details
     * @return EventDetails
     */
    public function create(EventDetails $details): EventDetails;


    /**
     * delete event details
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
