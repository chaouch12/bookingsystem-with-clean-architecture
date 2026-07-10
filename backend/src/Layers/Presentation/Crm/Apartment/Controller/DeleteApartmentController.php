<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Crm\Apartment\Controller;

use App\Layers\Application\Apartments\DeleteApartment\DeleteApartmentCommand;
use App\Layers\Application\Apartments\DeleteApartment\DeleteApartmentCommandHandler;
use App\Layers\Presentation\Crm\Apartment\Models\Response\ApiMessageResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Apartment')]
final class DeleteApartmentController extends AbstractController
{
    public function __construct(
        private readonly DeleteApartmentCommandHandler $handler,
    ) {
    }

    #[Route('/api/apartment/{id}', name: 'api_apartment_delete', methods: ['DELETE'])]
    #[OA\Delete(description: 'Deletes an apartment by id.', summary: 'Delete apartment')]
    #[OA\Parameter(name: 'id', description: 'Apartment id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1))]
    #[OA\Response(response: 204, description: 'Apartment deleted')]
    #[OA\Response(response: 404, description: 'Apartment not found', content: new OA\JsonContent(ref: new Model(type: ApiMessageResponse::class)))]
    public function __invoke(int $id): JsonResponse
    {
        $result = $this->handler->handle(new DeleteApartmentCommand($id));

        if ($result->isFailure()) {
            return $this->json(new ApiMessageResponse('Appartment not found.'), Response::HTTP_NOT_FOUND);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
