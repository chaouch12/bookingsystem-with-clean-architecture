<?php

declare(strict_types=1);

namespace App\Layers\Domain\Appartment;

use App\Layers\Domain\Appartment\Enum\Currency;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
readonly class Money
{
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    public string $amount;

    #[ORM\Column(length: 3, enumType: Currency::class)]
    public Currency $currency;

    public function __construct(
        string $amount,
        Currency $currency,
    ) {
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException('Amount must be numeric.');
        }

        if ((float) $amount < 0) {
            throw new InvalidArgumentException('Amount must not be negative.');
        }

        $this->amount = number_format((float) $amount, 2, '.', '');
        $this->currency = $currency;
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Currencies have to be equal.');
        }

        return new self(
            number_format((float) $this->amount + (float) $other->amount, 2, '.', ''),
            $this->currency,
        );

    }

    public static function zero(): self
    {
        return new self('0.00', Currency::NONE);
    }

}
