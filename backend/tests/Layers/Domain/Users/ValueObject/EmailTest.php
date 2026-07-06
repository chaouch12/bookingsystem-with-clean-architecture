<?php

declare(strict_types=1);

namespace App\Tests\Layers\Domain\Users\ValueObject;

use App\Layers\Domain\Users\ValueObject\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testItNormalizesEmailValues(): void
    {
        $email = new Email('  JOHN.DOE@Example.COM ');

        self::assertSame('john.doe@example.com', $email->value);
        self::assertSame('john.doe@example.com', (string) $email);
    }

    public function testItRejectsInvalidEmailValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Email('not-an-email');
    }
}
