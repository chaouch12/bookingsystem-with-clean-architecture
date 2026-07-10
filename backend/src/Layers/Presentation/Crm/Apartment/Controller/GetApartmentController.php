<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Crm\Apartment\Controller;

use App\Layers\Application\Apartments\GetApartment\GetApartmentQuery;
use App\Layers\Application\Apartments\GetApartment\GetApartmentQueryHandler;
use App\Layers\Presentation\Crm\Apartment\Mapper\ApartmentPresentationMapper;
use App\Layers\Presentation\Crm\Apartment\Models\Response\ApartmentResponse;
use App\Layers\Presentation\Crm\Apartment\Models\Response\ApiMessageResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Apartment')]
final class GetApartmentController extends AbstractController
{
    public function __construct(
        private readonly GetApartmentQueryHandler $handler,
        private readonly ApartmentPresentationMapper $mapper,
    ) {
    }

    #[Route('/api/apartment/{id}', name: 'api_apartment_show', methods: ['GET'])]
    #[OA\Get(description: 'Returns a single apartment by id.', summary: 'Get apartment')]
    #[OA\Parameter(name: 'id', description: 'Apartment id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1))]
    #[OA\Response(response: 200, description: 'Apartment details', content: new OA\JsonContent(ref: new Model(type: ApartmentResponse::class)))]
    #[OA\Response(response: 404, description: 'Apartment not found', content: new OA\JsonContent(ref: new Model(type: ApiMessageResponse::class)))]
    public function __invoke(int $id): JsonResponse
    {
        $result = $this->handler->handle(new GetApartmentQuery($id));

        if ($result->isFailure()) {
            return $this->json(new ApiMessageResponse('Appartment not found.'), Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->mapper->toResponse($result->value()));
    }
}
