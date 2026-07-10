<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\Shared;

use App\Layers\Domain\Appartment\Entity\Appartment;
use DateTimeInterface;

final class ApartmentViewFactory
{
    public function fromEntity(Appartment $appartment): ApartmentView
    {
        $address = $appartment->getAddress();

        return new ApartmentView(
            $appartment->getId(),
            $appartment->getName(),
            $appartment->getDescription(),
            $appartment->getPrice()->amount,
            $appartment->getPrice()->currency->value,
            $appartment->getCleaningFee()->amount,
            $appartment->getCleaningFee()->currency->value,
            $appartment->getLastBookedOnUtc()?->format(DateTimeInterface::ATOM),
            array_map(
                static fn ($amenity): int => $amenity->value,
                $appartment->getAmenities(),
            ),
            $address->street,
            $address->streetNumber,
            $address->zipcode,
            $address->city,
        );
    }
}
