<?php

declare(strict_types=1);

namespace App\Layers\Domain\Users\Event;

use App\Layers\Domain\Shared\Event\DomainEvent;
use App\Layers\Domain\Users\Enum\UserRoleType;
use DateTimeImmutable;

final readonly class UserRoleChanged implements DomainEvent
{
    public function __construct(
        public UserRoleType $oldRole,
        public UserRoleType $newRole,
        private DateTimeImmutable $occurredOn = new DateTimeImmutable(),
    ) {
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public static function eventName(): string
    {
        return 'user.role_changed';
    }
}
