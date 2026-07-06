<?php

declare(strict_types=1);

namespace App\Layers\Domain\Users\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final readonly class Email
{
    #[ORM\Column(name: 'user_email', length: 255, nullable: false)]
    public string $value;

    public function __construct(string $value)
    {
        $normalizedValue = mb_strtolower(trim($value));

        if (!filter_var($normalizedValue, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email.');
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
