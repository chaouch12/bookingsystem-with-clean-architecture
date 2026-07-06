<?php

declare(strict_types=1);

namespace App\Tests\Layers\Domain\Booking\Entity;

use App\Layers\Domain\Appartment\Enum\Currency;
use App\Layers\Domain\Appartment\Money;
use App\Layers\Domain\Booking\Entity\Booking;
use App\Layers\Domain\Booking\Enum\BookingStatus;
use App\Layers\Domain\Booking\Event\BookingCreated;
use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use App\Layers\Domain\Booking\ValueObject\GuestCount;
use PHPUnit\Framework\TestCase;

final class BookingTest extends TestCase
{
    public function testItCreatesPendingBookingAndRecordsDomainEvent(): void
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
        self::assertSame(BookingStatus::PENDING, $booking->getStatus());
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
}
