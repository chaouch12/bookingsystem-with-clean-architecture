<?php

declare(strict_types=1);

namespace App\Layers\Application\Booking\CreateBooking;

use App\Layers\Application\Shared\Event\DomainEventHandler;
use App\Layers\Application\Shared\Notification\EmailService;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use App\Layers\Domain\Booking\Event\BookingCreated;
use App\Layers\Domain\Shared\Event\DomainEvent;
use App\Layers\Domain\Users\Repository\UserRepository;

/**
 * @implements DomainEventHandler<BookingCreated>
 */
final readonly class BookingCreatedDomainEventHandler implements DomainEventHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private AppartmentRepository $appartmentRepository,
        private EmailService $emailService,
    ) {
    }

    public function handle(DomainEvent $event): void
    {
        $user = $this->userRepository->find($event->guestUserId);

        if ($user === null) {
            return;
        }

        $appartment = $this->appartmentRepository->find($event->appartmentId);

        if ($appartment === null) {
            return;
        }

        $this->emailService->send(
            $user->getEmail(),
            'Booking reserved!',
            sprintf(
                'Your booking for %s from %s to %s is currently %s.',
                $appartment->getName(),
                $event->checkIn->format('Y-m-d'),
                $event->checkOut->format('Y-m-d'),
                $event->status->value,
            ),
        );
    }
}
