<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Crm\Apartment\Controller;

use App\Layers\Application\Apartments\UpdateApartment\UpdateApartmentCommandHandler;
use App\Layers\Presentation\Crm\Apartment\Mapper\ApartmentPresentationMapper;
use App\Layers\Presentation\Crm\Apartment\Models\Payload\UpdateApartmentPayload;
use App\Layers\Presentation\Crm\Apartment\Models\Response\ApartmentResponse;
use App\Layers\Presentation\Crm\Apartment\Models\Response\ApiMessageResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Apartment')]
final class UpdateApartmentController extends AbstractController
{
    public function __construct(
        private readonly UpdateApartmentCommandHandler $handler,
        private readonly ApartmentPresentationMapper $mapper,
    ) {
    }

    #[Route('/api/apartment/{id}', name: 'api_apartment_update', methods: ['PUT'])]
    #[OA\Put(description: 'Updates an existing apartment.', summary: 'Update apartment')]
    #[OA\Parameter(name: 'id', description: 'Apartment id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: UpdateApartmentPayload::class)))]
    #[OA\Response(response: 200, description: 'Updated apartment', content: new OA\JsonContent(ref: new Model(type: ApartmentResponse::class)))]
    #[OA\Response(response: 404, description: 'Apartment not found', content: new OA\JsonContent(ref: new Model(type: ApiMessageResponse::class)))]
    public function __invoke(int $id, #[MapRequestPayload] UpdateApartmentPayload $payload): JsonResponse
    {
        $result = $this->handler->handle($this->mapper->toUpdateCommand($id, $payload));

        if ($result->isFailure()) {
            return $this->json(new ApiMessageResponse('Appartment not found.'), Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->mapper->toResponse($result->value()));
    }
}
