<?php

declare(strict_types=1);

namespace App\Layers\Domain\Users\Event;

use App\Layers\Domain\Shared\Event\DomainEvent;
use App\Layers\Domain\Users\Enum\UserRoleType;
use DateTimeImmutable;

final readonly class UserRegistered implements DomainEvent
{
    public function __construct(
        public string $email,
        public string $name,
        public UserRoleType $role,
        private DateTimeImmutable $occurredOn = new DateTimeImmutable(),
    ) {
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public static function eventName(): string
    {
        return 'user.registered';
    }
}
