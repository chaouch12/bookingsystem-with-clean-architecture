<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\SearchApartments;

use App\Layers\Application\Shared\Messaging\QueryHandler;
use App\Layers\Domain\Shared\ResultWithValue;

/**
 * @implements QueryHandler<SearchApartmentQuery, ResultWithValue<list<SearchApartmentResponse>>>
 */
final readonly class SearchApartmentQueryHandler implements QueryHandler
{
    public function __construct(
        private SearchApartmentReadRepository $searchApartmentReadRepository,
    ) {
    }

    /**
     * @return ResultWithValue<list<SearchApartmentResponse>>
     */
    public function handle(object $query): ResultWithValue
    {
        if ($query->startDate > $query->endDate) {
            return ResultWithValue::successWithValue($this->emptyResponseList());
        }

        return ResultWithValue::successWithValue(
            $this->searchApartmentReadRepository->searchAvailable(
                $query->startDate,
                $query->endDate,
            ),
        );
    }

    /**
     * @return list<SearchApartmentResponse>
     */
    private function emptyResponseList(): array
    {
        return [];
    }
}
