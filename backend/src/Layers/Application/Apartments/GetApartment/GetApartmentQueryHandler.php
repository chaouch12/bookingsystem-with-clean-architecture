<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\GetApartment;

use App\Layers\Application\Apartments\Shared\ApartmentView;
use App\Layers\Application\Apartments\Shared\ApartmentViewFactory;
use App\Layers\Application\Shared\Messaging\QueryHandler;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Appartment\AppartmentErrors;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use App\Layers\Domain\Shared\ResultWithValue;

/**
 * @implements QueryHandler<GetApartmentQuery, ResultWithValue<ApartmentView>>
 */
final readonly class GetApartmentQueryHandler implements QueryHandler
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
    public function handle(object $query): ResultWithValue
    {
        $this->messageValidator->validate($query);

        $appartment = $this->appartmentRepository->find($query->id);

        if ($appartment === null) {
            return ResultWithValue::failureWithError(AppartmentErrors::notFound());
        }

        return ResultWithValue::successWithValue($this->apartmentViewFactory->fromEntity($appartment));
    }
}
