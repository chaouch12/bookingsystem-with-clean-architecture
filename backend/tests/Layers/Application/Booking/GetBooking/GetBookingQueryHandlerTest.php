<?php

declare(strict_types=1);

namespace App\Tests\Layers\Application\Booking\GetBooking;

use App\Layers\Application\Booking\GetBooking\BookingResponse;
use App\Layers\Application\Booking\GetBooking\GetBookingQuery;
use App\Layers\Application\Booking\GetBooking\GetBookingQueryHandler;
use App\Layers\Application\Booking\GetBooking\GetBookingReadRepository;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Booking\BookingErrors;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class GetBookingQueryHandlerTest extends TestCase
{
    public function testItReturnsFlatBookingResponseWhenRecordExists(): void
    {
        $readRepository = $this->createMock(GetBookingReadRepository::class);
        $readRepository
            ->expects(self::once())
            ->method('findById')
            ->with(5)
            ->willReturn(new BookingResponse(
                5,
                10,
                20,
                'reserved',
                '480.00',
                'EUR',
                '35.00',
                'EUR',
                '24.00',
                'EUR',
                '539.00',
                'EUR',
                '2026-08-01',
                '2026-08-05',
                2,
                '2026-07-06 10:00:00',
                null,
                null,
                null,
            ));

        $result = (new GetBookingQueryHandler(
            $readRepository,
            new MessageValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
        ))->handle(new GetBookingQuery(5));

        self::assertTrue($result->isSuccess);
        self::assertInstanceOf(BookingResponse::class, $result->value());
        self::assertSame(5, $result->value()->id);
        self::assertSame(10, $result->value()->appartmentId);
        self::assertSame('reserved', $result->value()->status);
    }

    public function testItReturnsFailureWhenBookingDoesNotExist(): void
    {
        $readRepository = $this->createMock(GetBookingReadRepository::class);
        $readRepository
            ->expects(self::once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        $result = (new GetBookingQueryHandler(
            $readRepository,
            new MessageValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
        ))->handle(new GetBookingQuery(999));

        self::assertTrue($result->isFailure());
        self::assertSame(BookingErrors::notFound()->code, $result->error->code);
    }

    public function testItRejectsInvalidBookingIdBeforeCallingReadRepository(): void
    {
        $readRepository = $this->createMock(GetBookingReadRepository::class);
        $readRepository->expects(self::never())->method('findById');

        $handler = new GetBookingQueryHandler(
            $readRepository,
            new MessageValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
        );

        $this->expectException(ValidationFailedException::class);

        $handler->handle(new GetBookingQuery(0));
    }
}
