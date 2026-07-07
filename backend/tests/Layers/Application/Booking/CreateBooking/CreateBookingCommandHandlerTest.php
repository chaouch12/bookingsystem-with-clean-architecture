<?php

declare(strict_types=1);

namespace App\Tests\Layers\Application\Booking\CreateBooking;

use App\Layers\Application\Booking\CreateBooking\CreateBookingCommand;
use App\Layers\Application\Booking\CreateBooking\CreateBookingCommandHandler;
use App\Layers\Application\Shared\Event\DomainEventDispatcher;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use App\Layers\Domain\Booking\Repository\BookingRepository;
use App\Layers\Domain\Booking\Service\BookingPricingService;
use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use App\Layers\Domain\Booking\ValueObject\GuestCount;
use App\Layers\Domain\Users\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class CreateBookingCommandHandlerTest extends TestCase
{
    public function testItRejectsInvalidCommandBeforeTouchingRepositories(): void
    {
        $appartmentRepository = $this->createMock(AppartmentRepository::class);
        $appartmentRepository->expects(self::never())->method('find');

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects(self::never())->method('find');

        $bookingRepository = new BookingRepository($this->createStub(ManagerRegistry::class));
        $bookingPricingService = new BookingPricingService();
        $domainEventDispatcher = $this->createStub(DomainEventDispatcher::class);

        $handler = new CreateBookingCommandHandler(
            $appartmentRepository,
            $userRepository,
            $bookingRepository,
            $bookingPricingService,
            $domainEventDispatcher,
            new MessageValidator(
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator(),
            ),
        );

        $this->expectException(ValidationFailedException::class);

        $handler->handle(new CreateBookingCommand(
            0,
            0,
            new BookingPeriod(
                new \DateTimeImmutable('2026-08-01'),
                new \DateTimeImmutable('2026-08-05'),
            ),
            new GuestCount(2),
        ));
    }
}
