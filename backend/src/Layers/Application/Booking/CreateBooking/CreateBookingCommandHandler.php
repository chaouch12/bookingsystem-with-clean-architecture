<?php

declare(strict_types=1);

namespace App\Layers\Application\Booking\CreateBooking;

use App\Layers\Application\Shared\Messaging\CommandHandler;
use App\Layers\Application\Shared\Persistence\TransactionManager;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Appartment\AppartmentErrors;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use App\Layers\Domain\Booking\BookingErrors;
use App\Layers\Domain\Booking\Entity\Booking;
use App\Layers\Domain\Booking\Repository\BookingRepository;
use App\Layers\Domain\Booking\Service\BookingPricingService;
use App\Layers\Domain\Shared\ResultWithValue;
use App\Layers\Domain\Users\Repository\UserRepository;
use App\Layers\Domain\Users\UserErrors;

/**
 * @implements CommandHandler<CreateBookingCommand, ResultWithValue<int>>
 */
final readonly class CreateBookingCommandHandler implements CommandHandler
{
    public function __construct(
        private AppartmentRepository $appartmentRepository,
        private UserRepository $userRepository,
        private BookingRepository $bookingRepository,
        private BookingPricingService $bookingPricingService,
        private TransactionManager $transactionManager,
        private MessageValidator $messageValidator,
    ) {
    }

    /**
     * @return ResultWithValue<int>
     */
    public function handle(object $command): ResultWithValue
    {
        $this->messageValidator->validate($command);

        $appartment = $this->appartmentRepository->find($command->appartmentId);

        if ($appartment === null) {
            return ResultWithValue::failureWithError(AppartmentErrors::notFound());
        }

        $user = $this->userRepository->find($command->guestUserId);

        if ($user === null) {
            return ResultWithValue::failureWithError(UserErrors::notFound());
        }

        if ($this->bookingRepository->existsOverlapForAppartment($appartment->getId(), $command->period)) {
            return ResultWithValue::failureWithError(BookingErrors::overlap());
        }

        $pricingDetails = $this->bookingPricingService->calculatePrice($appartment, $command->period);

        $booking = Booking::create(
            $appartment->getId(),
            $user->getId(),
            $command->period,
            $command->guestCount,
            $pricingDetails->priceForPeriod,
            $pricingDetails->cleaningFee,
            $pricingDetails->amenitiesUpCharge,
            $pricingDetails->totalPrice,
        );

        $this->bookingRepository->save($booking, false);
        $this->transactionManager->flushAndPublish();

        return ResultWithValue::successWithValue($booking->getId());
    }
}
