<?php

declare(strict_types=1);

namespace App\Layers\Application\Booking\GetBooking;

use App\Layers\Application\Shared\Messaging\QueryHandler;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Booking\BookingErrors;
use App\Layers\Domain\Shared\ResultWithValue;

/**
 * @implements QueryHandler<GetBookingQuery, ResultWithValue<BookingResponse>>
 */
final readonly class GetBookingQueryHandler implements QueryHandler
{
    public function __construct(
        private GetBookingReadRepository $getBookingReadRepository,
        private MessageValidator $messageValidator,
    ) {
    }

    /**
     * @return ResultWithValue<BookingResponse>
     */
    public function handle(object $query): ResultWithValue
    {
        $this->messageValidator->validate($query);

        $booking = $this->getBookingReadRepository->findById($query->bookingId);

        if ($booking === null) {
            return ResultWithValue::failureWithError(BookingErrors::notFound());
        }

        return ResultWithValue::successWithValue($booking);
    }
}
