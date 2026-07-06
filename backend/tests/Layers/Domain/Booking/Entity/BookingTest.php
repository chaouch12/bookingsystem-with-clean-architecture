<?php

declare(strict_types=1);

namespace App\Tests\Layers\Domain\Booking\Entity;

use App\Layers\Domain\Appartment\Enum\Currency;
use App\Layers\Domain\Appartment\Money;
use App\Layers\Domain\Booking\BookingErrors;
use App\Layers\Domain\Booking\Entity\Booking;
use App\Layers\Domain\Booking\Enum\BookingStatus;
use App\Layers\Domain\Booking\Event\BookingCancelled;
use App\Layers\Domain\Booking\Event\BookingConfirmed;
use App\Layers\Domain\Booking\Event\BookingCreated;
use App\Layers\Domain\Booking\Event\BookingRejected;
use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use App\Layers\Domain\Booking\ValueObject\GuestCount;
use PHPUnit\Framework\TestCase;

final class BookingTest extends TestCase
{
    public function testItCreatesReservedBookingAndRecordsDomainEvent(): void
    {
        $booking = Booking::create(
            10,
            20,
            new BookingPeriod(new \DateTimeImmutable('2026-08-01'), new \DateTimeImmutable('2026-08-05')),
            new GuestCount(2),
            new Money('480.00', Currency::EUR),
            new Money('35.00', Currency::EUR),
            new Money('24.00', Currency::EUR),
            new Money('515.00', Currency::EUR),
        );

        self::assertSame(10, $booking->getAppartmentId());
        self::assertSame(20, $booking->getGuestUserId());
        self::assertSame(4, $booking->getPeriod()->nights());
        self::assertSame(2, $booking->getGuestCount()->value);
        self::assertSame(BookingStatus::RESERVED, $booking->getStatus());
        self::assertSame('480.00', $booking->getPriceForPeriod()->amount);
        self::assertSame('35.00', $booking->getCleaningFee()->amount);
        self::assertSame('24.00', $booking->getAmenitiesUpCharge()->amount);
        self::assertSame('515.00', $booking->getTotalPrice()->amount);

        $events = $booking->releaseDomainEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(BookingCreated::class, $events[0]);
        self::assertSame(10, $events[0]->appartmentId);
        self::assertSame(20, $events[0]->guestUserId);
    }

    public function testItConfirmsReservedBooking(): void
    {
        $booking = $this->makeBooking();
        $booking->setId(99);
        $booking->releaseDomainEvents();
        $now = new \DateTimeImmutable('2026-07-06 10:00:00');

        $result = $booking->confirm($now);

        self::assertTrue($result->isSuccess);
        self::assertSame(BookingStatus::CONFIRMED, $booking->getStatus());
        self::assertSame($now, $booking->getConfirmedOnUtc());
        self::assertInstanceOf(BookingConfirmed::class, $booking->releaseDomainEvents()[0]);
    }

    public function testItRejectsReservedBooking(): void
    {
        $booking = $this->makeBooking();
        $booking->setId(77);
        $booking->releaseDomainEvents();
        $now = new \DateTimeImmutable('2026-07-06 10:00:00');

        $result = $booking->reject($now);

        self::assertTrue($result->isSuccess);
        self::assertSame(BookingStatus::REJECTED, $booking->getStatus());
        self::assertSame($now, $booking->getRejectedOnUtc());
        self::assertInstanceOf(BookingRejected::class, $booking->releaseDomainEvents()[0]);
    }

    public function testItCannotConfirmNonReservedBooking(): void
    {
        $booking = $this->makeBooking();
        $booking->setId(66);
        $booking->releaseDomainEvents();
        $booking->reject(new \DateTimeImmutable('2026-07-06 10:00:00'));
        $booking->releaseDomainEvents();

        $result = $booking->confirm(new \DateTimeImmutable('2026-07-06 11:00:00'));

        self::assertTrue($result->isFailure());
        self::assertSame(BookingErrors::notPending()->code, $result->error->code);
    }

    public function testItCancelsConfirmedBookingBeforeStartDate(): void
    {
        $booking = $this->makeBooking();
        $booking->setId(55);
        $booking->releaseDomainEvents();
        $booking->confirm(new \DateTimeImmutable('2026-07-06 10:00:00'));
        $booking->releaseDomainEvents();
        $cancelTime = new \DateTimeImmutable('2026-07-30 12:00:00');

        $result = $booking->cancel($cancelTime);

        self::assertTrue($result->isSuccess);
        self::assertSame(BookingStatus::CANCELLED, $booking->getStatus());
        self::assertSame($cancelTime, $booking->getCancelledOnUtc());
        self::assertInstanceOf(BookingCancelled::class, $booking->releaseDomainEvents()[0]);
    }

    public function testItCannotCancelUnconfirmedBooking(): void
    {
        $booking = $this->makeBooking();
        $booking->releaseDomainEvents();

        $result = $booking->cancel(new \DateTimeImmutable('2026-07-30 12:00:00'));

        self::assertTrue($result->isFailure());
        self::assertSame(BookingErrors::notConfirmed()->code, $result->error->code);
    }

    public function testItCannotCancelStartedBooking(): void
    {
        $booking = $this->makeBooking();
        $booking->setId(11);
        $booking->releaseDomainEvents();
        $booking->confirm(new \DateTimeImmutable('2026-07-06 10:00:00'));
        $booking->releaseDomainEvents();

        $result = $booking->cancel(new \DateTimeImmutable('2026-08-02 12:00:00'));

        self::assertTrue($result->isFailure());
        self::assertSame(BookingErrors::alreadyStarted()->code, $result->error->code);
    }

    private function makeBooking(): Booking
    {
        return Booking::create(
            10,
            20,
            new BookingPeriod(new \DateTimeImmutable('2026-08-01'), new \DateTimeImmutable('2026-08-05')),
            new GuestCount(2),
            new Money('480.00', Currency::EUR),
            new Money('35.00', Currency::EUR),
            new Money('24.00', Currency::EUR),
            new Money('515.00', Currency::EUR),
        );
    }
}
