<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\UpdateApartment;

use App\Layers\Application\Apartments\Shared\ApartmentView;
use App\Layers\Application\Apartments\Shared\ApartmentViewFactory;
use App\Layers\Application\Shared\Messaging\CommandHandler;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Appartment\AppartmentErrors;
use App\Layers\Domain\Appartment\Entity\Embeddable\Address;
use App\Layers\Domain\Appartment\Enum\Amenity;
use App\Layers\Domain\Appartment\Enum\Currency;
use App\Layers\Domain\Appartment\Money;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use App\Layers\Domain\Shared\ResultWithValue;
use DateTimeImmutable;

/**
 * @implements CommandHandler<UpdateApartmentCommand, ResultWithValue<ApartmentView>>
 */
final readonly class UpdateApartmentCommandHandler implements CommandHandler
{
    public function __construct(
        private AppartmentRepository $appartmentRepository,
        private ApartmentViewFactory $apartmentViewFactory,
        private MessageValidator $messageValidator,
    ) {
    }

    /**
     * @return ResultWithValue<ApartmentView>
     */
    public function handle(object $command): ResultWithValue
    {
        $this->messageValidator->validate($command);

        $appartment = $this->appartmentRepository->find($command->id);

        if ($appartment === null) {
            return ResultWithValue::failureWithError(AppartmentErrors::notFound());
        }

        $appartment
            ->setName($command->name)
            ->setDescription($command->description)
            ->setPrice(new Money($command->priceAmount, Currency::from($command->priceCurrency)))
            ->setCleaningFee(new Money($command->cleaningFeeAmount, Currency::from($command->cleaningFeeCurrency)))
            ->setLastBookedOnUtc($command->lastBookedOnUtc !== null ? new DateTimeImmutable($command->lastBookedOnUtc) : null)
            ->setAmenities(array_map(static fn (int $amenity): Amenity => Amenity::from($amenity), $command->amenities))
            ->setAddress(new Address($command->street, $command->streetNumber, $command->zipcode, $command->city));

        $this->appartmentRepository->save($appartment, true);

        return ResultWithValue::successWithValue($this->apartmentViewFactory->fromEntity($appartment));
    }
}
