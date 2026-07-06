<?php

declare(strict_types=1);

namespace App\Layers\Domain\Booking\ValueObject;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final readonly class BookingPeriod
{
    #[ORM\Column(name: 'check_in', type: Types::DATE_IMMUTABLE)]
    public DateTimeImmutable $checkIn;

    #[ORM\Column(name: 'check_out', type: Types::DATE_IMMUTABLE)]
    public DateTimeImmutable $checkOut;

    public function __construct(DateTimeImmutable $checkIn, DateTimeImmutable $checkOut)
    {
        $normalizedCheckIn = $checkIn->setTime(0, 0);
        $normalizedCheckOut = $checkOut->setTime(0, 0);

        if ($normalizedCheckOut <= $normalizedCheckIn) {
            throw new InvalidArgumentException('Check-out must be after check-in.');
        }

        $this->checkIn = $normalizedCheckIn;
        $this->checkOut = $normalizedCheckOut;
    }

    public function nights(): int
    {
        return $this->checkIn->diff($this->checkOut)->days;
    }

    public function overlaps(self $other): bool
    {
        return $this->checkIn < $other->checkOut && $this->checkOut > $other->checkIn;
    }
}
