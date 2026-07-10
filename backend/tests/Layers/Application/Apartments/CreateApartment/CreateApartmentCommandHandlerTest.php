<?php

declare(strict_types=1);

namespace App\Tests\Layers\Application\Apartments\CreateApartment;

use App\Layers\Application\Apartments\CreateApartment\CreateApartmentCommand;
use App\Layers\Application\Apartments\CreateApartment\CreateApartmentCommandHandler;
use App\Layers\Application\Apartments\Shared\ApartmentViewFactory;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Appartment\Entity\Appartment;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class CreateApartmentCommandHandlerTest extends TestCase
{
    public function testItCreatesApartmentAndReturnsApplicationView(): void
    {
        $repository = $this->createMock(AppartmentRepository::class);
        $repository->expects(self::once())
            ->method('save')
            ->with(self::isInstanceOf(Appartment::class), true)
            ->willReturnCallback(function (Appartment $appartment): void {
                $reflection = new \ReflectionProperty($appartment, 'id');
                $reflection->setValue($appartment, 10);
            });

        $handler = new CreateApartmentCommandHandler(
            $repository,
            new ApartmentViewFactory(),
            new MessageValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
        );

        $result = $handler->handle(new CreateApartmentCommand(
            'Mountain Loft',
            'Nice stay',
            '100.00',
            'EUR',
            '20.00',
            'EUR',
            '2026-08-01T10:00:00+00:00',
            [1, 3],
            'Main Street',
            '1',
            '12345',
            'Berlin',
        ));

        self::assertTrue($result->isSuccess);
        self::assertSame(10, $result->value()->id);
        self::assertSame('Mountain Loft', $result->value()->name);
        self::assertSame([1, 3], $result->value()->amenities);
    }
}
