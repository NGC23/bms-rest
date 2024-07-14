<?php

declare(strict_types=1);

namespace App\Domain\Booking\Interfaces;

use App\Domain\Booking\Model\BookerDetails;

interface IBookingDetailsRepository
{
    /**
     * Undocumented function
     *
     * @param BookerDetails $bookerDetails
     * @return BookerDetails
     */
    public function create(BookerDetails $bookerDetails): BookerDetails;
}
