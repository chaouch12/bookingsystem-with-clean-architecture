<?php

declare(strict_types=1);

namespace App\Layers\Application\Shared\Messaging;

/**
 * @template TQuery of Query
 * @template TResult
 */
interface QueryHandler
{
    /**
     * @param TQuery $query
     *
     * @return TResult
     */
    public function handle(object $query): mixed;
}
