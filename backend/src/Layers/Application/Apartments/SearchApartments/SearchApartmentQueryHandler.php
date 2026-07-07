<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\SearchApartments;

use App\Layers\Application\Shared\Messaging\QueryHandler;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Shared\ResultWithValue;

/**
 * @implements QueryHandler<SearchApartmentQuery, ResultWithValue<list<SearchApartmentResponse>>>
 */
final readonly class SearchApartmentQueryHandler implements QueryHandler
{
    public function __construct(
        private SearchApartmentReadRepository $searchApartmentReadRepository,
        private MessageValidator $messageValidator,
    ) {
    }

    /**
     * @return ResultWithValue<list<SearchApartmentResponse>>
     */
    public function handle(object $query): ResultWithValue
    {
        $this->messageValidator->validate($query);

        return ResultWithValue::successWithValue(
            $this->searchApartmentReadRepository->searchAvailable(
                $query->startDate,
                $query->endDate,
            ),
        );
    }
}
