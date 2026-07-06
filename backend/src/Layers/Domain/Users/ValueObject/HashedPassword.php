<?php

declare(strict_types=1);

namespace App\Layers\Domain\Users\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use SensitiveParameter;

#[ORM\Embeddable]
final readonly class HashedPassword
{
    #[ORM\Column(name: 'user_password', length: 40, nullable: false)]
    public string $value;

    public function __construct(#[SensitiveParameter] string $value)
    {
        $normalizedValue = trim($value);

        if ($normalizedValue === '') {
            throw new InvalidArgumentException('Password hash must not be empty.');
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
