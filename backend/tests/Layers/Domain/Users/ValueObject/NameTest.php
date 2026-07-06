<?php

declare(strict_types=1);

namespace App\Tests\Layers\Domain\Users\ValueObject;

use App\Layers\Domain\Users\ValueObject\Name;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class NameTest extends TestCase
{
    public function testItNormalizesUserNames(): void
    {
        $name = new Name('  Jane Doe  ');

        self::assertSame('Jane Doe', $name->value);
        self::assertSame('Jane Doe', (string) $name);
    }

    public function testItRejectsEmptyUserNames(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Name('   ');
    }
}
