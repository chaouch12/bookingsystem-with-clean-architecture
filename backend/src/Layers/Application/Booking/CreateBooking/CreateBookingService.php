<?php

declare(strict_types=1);

namespace App\src\Layers\Application\Booking\CreateBooking;

use App\Common\Exception\ConflictException;
use App\Common\Exception\NotFoundException;
use App\Layers\Domain\Booking\Entity\Booking;
use App\Layers\Domain\Booking\Repository\BookingRepository;
use App\Layers\Domain\Booking\Service\BookingPricingService;
use App\Layers\Domain\Users\Repository\UserRepository;
use App\src\Layers\Application\Shared\Event\DomainEventDispatcher;
use App\src\Layers\Domain\Appartment\Repo\AppartmentRepository;

final readonly class CreateBookingService
{
    public function __construct(
        private AppartmentRepository $appartmentRepository,
        private UserRepository $userRepository,
        private BookingRepository $bookingRepository,
        private BookingPricingService $bookingPricingService,
        private DomainEventDispatcher $domainEventDispatcher,
    ) {
    }

    public function handle(CreateBookingCommand $command): Booking
    {
        $appartment = $this->appartmentRepository->find($command->appartmentId);

        if ($appartment === null) {
            throw NotFoundException::forResource('Appartment', $command->appartmentId);
        }

        $user = $this->userRepository->find($command->guestUserId);

        if ($user === null) {
            throw NotFoundException::forResource('User', $command->guestUserId);
        }

        if ($this->bookingRepository->existsOverlapForAppartment($appartment->getId(), $command->period)) {
            throw ConflictException::because('Appartment is not available for the selected period.');
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

        $this->bookingRepository->save($booking, true);
        $this->domainEventDispatcher->dispatch(...$booking->releaseDomainEvents());

        return $booking;
    }
}
