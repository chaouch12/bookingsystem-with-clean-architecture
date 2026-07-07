<?php

declare(strict_types=1);

namespace App\Tests\Layers\Application\Shared\Validation;

use App\Layers\Application\Apartments\SearchApartments\SearchApartmentQuery;
use App\Layers\Application\Apartments\SearchApartments\SearchApartmentResponse;
use App\Layers\Application\Booking\CreateBooking\CreateBookingCommand;
use App\Layers\Application\Booking\GetBooking\BookingResponse;
use App\Layers\Application\Booking\GetBooking\GetBookingQuery;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Appartment\Facade\DTO\AddressDto;
use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use App\Layers\Domain\Booking\ValueObject\GuestCount;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class MessageValidatorTest extends TestCase
{
    private MessageValidator $messageValidator;

    protected function setUp(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $this->messageValidator = new MessageValidator($validator);
    }

    public function testItValidatesCreateBookingCommand(): void
    {
        $this->messageValidator->validate(
            new CreateBookingCommand(
                10,
                20,
                new BookingPeriod(
                    new \DateTimeImmutable('2026-08-01'),
                    new \DateTimeImmutable('2026-08-05'),
                ),
                new GuestCount(2),
            ),
        );

        self::assertTrue(true);
    }

    public function testItRejectsInvalidCreateBookingCommand(): void
    {
        $this->expectException(ValidationFailedException::class);

        $this->messageValidator->validate(
            new CreateBookingCommand(
                0,
                -1,
                new BookingPeriod(
                    new \DateTimeImmutable('2026-08-01'),
                    new \DateTimeImmutable('2026-08-05'),
                ),
                new GuestCount(2),
            ),
        );
    }

    public function testItRejectsInvalidGetBookingQuery(): void
    {
        $this->expectException(ValidationFailedException::class);

        $this->messageValidator->validate(new GetBookingQuery(0));
    }

    public function testItRejectsInvalidSearchApartmentQuery(): void
    {
        $this->expectException(ValidationFailedException::class);

        $this->messageValidator->validate(
            new SearchApartmentQuery(
                new \DateTimeImmutable('2026-08-05'),
                new \DateTimeImmutable('2026-08-01'),
            ),
        );
    }

    public function testItValidatesBookingResponseDto(): void
    {
        $this->messageValidator->validate(new BookingResponse(
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

        self::assertTrue(true);
    }

    public function testItValidatesSearchApartmentResponseDto(): void
    {
        $this->messageValidator->validate(new SearchApartmentResponse(
            10,
            'Mountain Loft',
            'Nice stay',
            '100.00',
            'EUR',
            '20.00',
            'EUR',
            'Main Street',
            '1',
            '12345',
            'Berlin',
        ));

        self::assertTrue(true);
    }

    public function testItRejectsInvalidAddressDto(): void
    {
        $this->expectException(ValidationFailedException::class);

        $this->messageValidator->validate(new AddressDto('', '', '', ''));
    }
}
