<?php

declare(strict_types=1);

namespace App\Tests\Layers\Application\Apartments\GetApartment;

use App\Layers\Application\Apartments\GetApartment\GetApartmentQuery;
use App\Layers\Application\Apartments\GetApartment\GetApartmentQueryHandler;
use App\Layers\Application\Apartments\Shared\ApartmentViewFactory;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Appartment\AppartmentErrors;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class GetApartmentQueryHandlerTest extends TestCase
{
    public function testItReturnsFailureWhenApartmentDoesNotExist(): void
    {
        $repository = $this->createMock(AppartmentRepository::class);
        $repository->expects(self::once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $handler = new GetApartmentQueryHandler(
            $repository,
            new ApartmentViewFactory(),
            new MessageValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
        );

        $result = $handler->handle(new GetApartmentQuery(999));

        self::assertTrue($result->isFailure());
        self::assertSame(AppartmentErrors::notFound()->code, $result->error->code);
    }
}
