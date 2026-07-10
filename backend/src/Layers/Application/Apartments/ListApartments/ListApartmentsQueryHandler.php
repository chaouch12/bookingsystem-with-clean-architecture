<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\ListApartments;

use App\Layers\Application\Apartments\Shared\ApartmentView;
use App\Layers\Application\Apartments\Shared\ApartmentViewFactory;
use App\Layers\Application\Shared\Messaging\QueryHandler;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Appartment\Entity\Appartment;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use App\Layers\Domain\Shared\ResultWithValue;

/**
 * @implements QueryHandler<ListApartmentsQuery, ResultWithValue<list<ApartmentView>>>
 */
final readonly class ListApartmentsQueryHandler implements QueryHandler
{
    public function __construct(
        private AppartmentRepository $appartmentRepository,
        private ApartmentViewFactory $apartmentViewFactory,
        private MessageValidator $messageValidator,
    ) {
    }

    /**
     * @return ResultWithValue<list<ApartmentView>>
     */
    public function handle(object $query): ResultWithValue
    {
        $this->messageValidator->validate($query);

        return ResultWithValue::successWithValue(
            array_map(
                fn (Appartment $appartment): ApartmentView => $this->apartmentViewFactory->fromEntity($appartment),
                $this->appartmentRepository->findAll(),
            ),
        );
    }
}
