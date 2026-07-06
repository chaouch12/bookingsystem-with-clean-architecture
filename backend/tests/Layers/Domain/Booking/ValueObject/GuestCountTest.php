<?php

declare(strict_types=1);

namespace App\Tests\Layers\Domain\Booking\ValueObject;

use App\Layers\Domain\Booking\ValueObject\GuestCount;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GuestCountTest extends TestCase
{
    public function testItRejectsZeroGuests(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GuestCount(0);
    }
}
