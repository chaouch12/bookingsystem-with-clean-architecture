<?php

declare(strict_types=1);

namespace App\Layers\Domain\Users\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final readonly class Name
{
    #[ORM\Column(name: 'user_name', length: 120, nullable: false)]
    public string $value;

    public function __construct(string $value)
    {
        $normalizedValue = trim($value);

        if ($normalizedValue === '') {
            throw new InvalidArgumentException('User name must not be empty.');
        }

        if (mb_strlen($normalizedValue) > 120) {
            throw new InvalidArgumentException('User name must not exceed 120 characters.');
        }

        $this->value = $normalizedValue;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
