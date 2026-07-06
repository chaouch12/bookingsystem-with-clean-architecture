<?php

declare(strict_types=1);

namespace App\Tests\Layers\Domain\Users;

use App\Layers\Domain\Users\User;
use App\Layers\Domain\Users\Enum\UserRoleType;
use App\Layers\Domain\Users\Enum\UserStatusType;
use App\Layers\Domain\Users\ValueObject\Email;
use App\Layers\Domain\Users\ValueObject\HashedPassword;
use App\Layers\Domain\Users\ValueObject\Name;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testItUsesValueObjectsAndExposesSecurityIdentifiers(): void
    {
        $user = User::create(
            new HashedPassword('$2y$13$abcdefghijklmnopqrstuvabcdefghijklmnopqrstuv1234567890'),
            UserStatusType::ADMIN,
            UserRoleType::MANAGER,
            new Email(' Manager@Example.com '),
            new Name('Manager One'),
        );

        self::assertSame('Manager One', $user->getName());
        self::assertSame('manager@example.com', $user->getEmail());
        self::assertSame('manager@example.com', $user->getUserIdentifier());
        self::assertSame('$2y$13$abcdefghijklmnopqrstuvabcdefghijklmnopqrstuv1234567890', $user->getPassword());
        self::assertSame('manager@example.com', $user->getEmailAddress()->value);
        self::assertSame('Manager One', $user->getUserName()->value);
    }
}
