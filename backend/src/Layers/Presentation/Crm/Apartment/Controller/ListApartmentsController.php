<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Crm\Apartment\Controller;

use App\Layers\Application\Apartments\ListApartments\ListApartmentsQuery;
use App\Layers\Application\Apartments\ListApartments\ListApartmentsQueryHandler;
use App\Layers\Application\Apartments\Shared\ApartmentView;
use App\Layers\Presentation\Crm\Apartment\Mapper\ApartmentPresentationMapper;
use App\Layers\Presentation\Crm\Apartment\Models\Response\ApartmentResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Apartment')]
final class ListApartmentsController extends AbstractController
{
    public function __construct(
        private readonly ListApartmentsQueryHandler $handler,
        private readonly ApartmentPresentationMapper $mapper,
    ) {
    }

    #[Route('/api/apartment', name: 'api_apartment_list', methods: ['GET'])]
    #[OA\Get(description: 'Returns all apartments.', summary: 'List apartments')]
    #[OA\Response(
        response: 200,
        description: 'Apartment list',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: ApartmentResponse::class)))
    )]
    public function __invoke(): JsonResponse
    {
        $result = $this->handler->handle(new ListApartmentsQuery());

        return $this->json(
            array_map(
                fn (ApartmentView $view): ApartmentResponse => $this->mapper->toResponse($view),
                $result->value(),
            ),
        );
    }
}
