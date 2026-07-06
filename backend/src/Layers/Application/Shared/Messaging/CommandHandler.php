<?php

declare(strict_types=1);

namespace App\Layers\Application\Shared\Messaging;

/**
 * @template TCommand of Command
 * @template TResult
 */
interface CommandHandler
{
    /**
     * @param TCommand $command
     *
     * @return TResult
     */
    public function handle(object $command): mixed;
}
