<?php

declare(strict_types=1);

namespace App\Tests\Layers\Domain\Booking\ValueObject;

use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BookingPeriodTest extends TestCase
{
    public function testItCalculatesNumberOfNights(): void
    {
        $period = new BookingPeriod(
            new \DateTimeImmutable('2026-08-01 15:00:00'),
            new \DateTimeImmutable('2026-08-05 09:00:00'),
        );

        self::assertSame(4, $period->nights());
    }

    public function testItRejectsInvalidRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new BookingPeriod(
            new \DateTimeImmutable('2026-08-05'),
            new \DateTimeImmutable('2026-08-05'),
        );
    }
}
