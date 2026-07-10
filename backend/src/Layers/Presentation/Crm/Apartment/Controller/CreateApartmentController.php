<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Crm\Apartment\Controller;

use App\Layers\Application\Apartments\CreateApartment\CreateApartmentCommandHandler;
use App\Layers\Presentation\Crm\Apartment\Mapper\ApartmentPresentationMapper;
use App\Layers\Presentation\Crm\Apartment\Models\Payload\CreateApartmentPayload;
use App\Layers\Presentation\Crm\Apartment\Models\Response\ApartmentResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Apartment')]
final class CreateApartmentController extends AbstractController
{
    public function __construct(
        private readonly CreateApartmentCommandHandler $handler,
        private readonly ApartmentPresentationMapper $mapper,
    ) {
    }

    #[Route('/api/apartment', name: 'api_apartment_create', methods: ['POST'])]
    #[OA\Post(description: 'Creates a new apartment entry.', summary: 'Create apartment')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: CreateApartmentPayload::class)))]
    #[OA\Response(response: 201, description: 'Apartment created', content: new OA\JsonContent(ref: new Model(type: ApartmentResponse::class)))]
    public function createApartment(#[MapRequestPayload] CreateApartmentPayload $payload): JsonResponse
    {
        $result = $this->handler->handle($this->mapper->toCreateCommand($payload));

        return $this->json($this->mapper->toResponse($result->value()), Response::HTTP_CREATED);
    }
}
