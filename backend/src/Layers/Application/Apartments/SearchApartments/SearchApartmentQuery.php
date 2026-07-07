<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\SearchApartments;

use App\Layers\Application\Shared\Messaging\Query;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final readonly class SearchApartmentQuery implements Query
{
    public function __construct(
        #[Assert\NotNull]
        public DateTimeImmutable $startDate,
        #[Assert\NotNull]
        public DateTimeImmutable $endDate,
    ) {
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->startDate > $this->endDate) {
            $context
                ->buildViolation('Start date must be earlier than or equal to end date.')
                ->atPath('startDate')
                ->addViolation();
        }
    }
}
