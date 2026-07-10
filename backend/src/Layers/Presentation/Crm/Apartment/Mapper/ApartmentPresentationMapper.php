<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Crm\Apartment\Mapper;

use App\Layers\Application\Apartments\CreateApartment\CreateApartmentCommand;
use App\Layers\Application\Apartments\Shared\ApartmentView;
use App\Layers\Application\Apartments\UpdateApartment\UpdateApartmentCommand;
use App\Layers\Domain\Appartment\Enum\Amenity;
use App\Layers\Presentation\Crm\Apartment\Models\Payload\CreateApartmentPayload;
use App\Layers\Presentation\Crm\Apartment\Models\Payload\UpdateApartmentPayload;
use App\Layers\Presentation\Crm\Apartment\Models\Response\AddressResponse;
use App\Layers\Presentation\Crm\Apartment\Models\Response\AmenityResponse;
use App\Layers\Presentation\Crm\Apartment\Models\Response\ApartmentResponse;
use App\Layers\Presentation\Crm\Apartment\Models\Response\MoneyResponse;

final class ApartmentPresentationMapper
{
    public function toCreateCommand(CreateApartmentPayload $payload): CreateApartmentCommand
    {
        return new CreateApartmentCommand(
            $payload->name,
            $payload->description,
            $payload->priceAmount,
            $payload->priceCurrency,
            $payload->cleaningFeeAmount,
            $payload->cleaningFeeCurrency,
            $payload->lastBookedOnUtc,
            $payload->amenities,
            $payload->street,
            $payload->streetNumber,
            $payload->zipcode,
            $payload->city,
        );
    }

    public function toUpdateCommand(int $id, UpdateApartmentPayload $payload): UpdateApartmentCommand
    {
        return new UpdateApartmentCommand(
            $id,
            $payload->name,
            $payload->description,
            $payload->priceAmount,
            $payload->priceCurrency,
            $payload->cleaningFeeAmount,
            $payload->cleaningFeeCurrency,
            $payload->lastBookedOnUtc,
            $payload->amenities,
            $payload->street,
            $payload->streetNumber,
            $payload->zipcode,
            $payload->city,
        );
    }

    public function toResponse(ApartmentView $view): ApartmentResponse
    {
        return new ApartmentResponse(
            $view->id,
            $view->name,
            $view->description,
            new MoneyResponse($view->priceAmount, $view->priceCurrency),
            new MoneyResponse($view->cleaningFeeAmount, $view->cleaningFeeCurrency),
            $view->lastBookedOnUtc,
            array_map(
                static fn (int $amenity): AmenityResponse => new AmenityResponse(
                    Amenity::from($amenity)->name,
                    $amenity,
                ),
                $view->amenities,
            ),
            new AddressResponse(
                $view->street,
                $view->streetNumber,
                $view->zipcode,
                $view->city,
                sprintf('%s %s, %s, %s', $view->street, $view->streetNumber, $view->zipcode, $view->city),
            ),
        );
    }
}
