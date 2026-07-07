<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Event;

use App\Layers\Domain\Users\Enum\UserRoleType;
use App\Layers\Domain\Users\Event\UserRegistered;
use App\Layers\Infrastructure\Event\InMemoryDomainEventDispatcher;
use PHPUnit\Framework\TestCase;

final class InMemoryDomainEventDispatcherTest extends TestCase
{
    public function testItDispatchesEventsToMatchingListeners(): void
    {
        $receivedEvents = [];
        $dispatcher = new InMemoryDomainEventDispatcher([
            UserRegistered::class => [
                static function (UserRegistered $event) use (&$receivedEvents): void {
                    $receivedEvents[] = $event->email;
                },
            ],
        ]);

        $dispatcher->dispatch(new UserRegistered('manager@example.com', 'Manager One', UserRoleType::MANAGER));

        self::assertSame(['manager@example.com'], $receivedEvents);
    }
}
