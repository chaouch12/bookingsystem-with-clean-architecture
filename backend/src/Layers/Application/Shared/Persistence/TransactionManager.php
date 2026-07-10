<?php

declare(strict_types=1);

namespace App\Layers\Application\Shared\Persistence;

interface TransactionManager
{
    public function flushAndPublish(): void;
}
