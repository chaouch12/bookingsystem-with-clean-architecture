<?php

declare(strict_types=1);

namespace App\Tests\Layers\Domain\Shared;

use App\Layers\Domain\Shared\Error;
use App\Layers\Domain\Shared\ResultWithValue;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ResultWithValueTest extends TestCase
{
    public function testItReturnsSuccessValue(): void
    {
        $result = ResultWithValue::successWithValue(42);

        self::assertTrue($result->isSuccess);
        self::assertSame(42, $result->value());
    }

    public function testItReturnsFailureWithError(): void
    {
        $result = ResultWithValue::failureWithError(new Error('Booking.Overlap', 'Overlap'));

        self::assertTrue($result->isFailure());
        self::assertSame('Booking.Overlap', $result->error->code);
    }

    public function testItDoesNotExposeFailureValue(): void
    {
        $result = ResultWithValue::failureWithError(new Error('Booking.Overlap', 'Overlap'));

        $this->expectException(RuntimeException::class);

        $result->value();
    }
}
