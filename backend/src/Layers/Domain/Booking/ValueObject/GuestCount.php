<?php

declare(strict_types=1);

namespace App\Layers\Domain\Booking\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final readonly class GuestCount
{
    #[ORM\Column(name: 'guest_count', options: ['unsigned' => true])]
    public int $value;

    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new InvalidArgumentException('Guest count must be at least 1.');
        }

        $this->value = $value;
    }
}
