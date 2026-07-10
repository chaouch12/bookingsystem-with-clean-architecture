<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Http\Controller;

use App\Layers\Domain\Appartment\Entity\Appartment;
use App\Layers\Domain\Appartment\Entity\Embeddable\Address;
use App\Layers\Domain\Appartment\Enum\Amenity;
use App\Layers\Domain\Appartment\Enum\Currency;
use App\Layers\Domain\Appartment\Money;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use App\Layers\Presentation\Http\Controller\Request\CreateApartmentRequest;
use App\Layers\Presentation\Http\Controller\Response\ApartmentResponse;
use App\Layers\Presentation\Http\Controller\Response\ApiMessageResponse;
use DateTimeImmutable;
use DateTimeInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class ApartmentController extends AbstractController
{
    public function __construct(
        private readonly AppartmentRepository $appartmentRepository,
    ) {
    }

    #[Route('/api/apartment', name: 'api_apartment_create', methods: ['POST'])]
    #[OA\Post(description: 'Creates a new apartment entry.', summary: 'Create apartment')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: CreateApartmentRequest::class))
    )]
    #[OA\Response(
        response: 201,
        description: 'Apartment created',
        content: new OA\JsonContent(ref: new Model(type: ApartmentResponse::class))
    )]
    #[OA\Tag(name: 'Apartment')]
    public function addAppartment(#[MapRequestPayload] CreateApartmentRequest $request): JsonResponse
    {
        $appartment = new Appartment(
            $request->name,
            $request->description,
            new Money($request->priceAmount, Currency::from($request->priceCurrency)),
            new Money($request->cleaningFeeAmount, Currency::from($request->cleaningFeeCurrency)),
            $request->lastBookedOnUtc !== null ? new DateTimeImmutable($request->lastBookedOnUtc) : null,
            array_map(static fn (int $amenity): Amenity => Amenity::from($amenity), $request->amenities),
            new Address($request->street, $request->streetNumber, $request->zipcode, $request->city),
        );

        $this->appartmentRepository->save($appartment, true);

        return $this->json($this->normalizeAppartment($appartment), Response::HTTP_CREATED);
    }

    #[Route('/api/apartment', name: 'api_apartment_list', methods: ['GET'])]
    #[OA\Get(summary: 'List apartments', description: 'Returns all apartments.')]
    #[OA\Response(
        response: 200,
        description: 'Apartment list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ApartmentResponse::class))
        )
    )]
    #[OA\Tag(name: 'Apartment')]
    public function listAppartments(): JsonResponse
    {
        return $this->json(
            array_map(
                fn (Appartment $appartment): array => $this->normalizeAppartment($appartment),
                $this->appartmentRepository->findAll(),
            ),
        );
    }

    #[Route('/api/apartment/{id}', name: 'api_apartment_show', methods: ['GET'])]
    #[OA\Get(summary: 'Get apartment', description: 'Returns a single apartment by id.')]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Apartment id', schema: new OA\Schema(type: 'integer', minimum: 1))]
    #[OA\Response(
        response: 200,
        description: 'Apartment details',
        content: new OA\JsonContent(ref: new Model(type: ApartmentResponse::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Apartment not found',
        content: new OA\JsonContent(ref: new Model(type: ApiMessageResponse::class))
    )]
    #[OA\Tag(name: 'Apartment')]
    public function getAppartment(int $id): JsonResponse
    {
        $appartment = $this->appartmentRepository->find($id);

        if ($appartment === null) {
            return $this->json(['message' => 'Appartment not found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->normalizeAppartment($appartment));
    }

    #[Route('/api/apartment/{id}', name: 'api_apartment_update', methods: ['PUT'])]
    #[OA\Put(summary: 'Update apartment', description: 'Updates an existing apartment.')]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Apartment id', schema: new OA\Schema(type: 'integer', minimum: 1))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: CreateApartmentRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: 'Updated apartment',
        content: new OA\JsonContent(ref: new Model(type: ApartmentResponse::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Apartment not found',
        content: new OA\JsonContent(ref: new Model(type: ApiMessageResponse::class))
    )]
    #[OA\Tag(name: 'Apartment')]
    public function updateAppartment(int $id, #[MapRequestPayload] CreateApartmentRequest $request): JsonResponse
    {
        $appartment = $this->appartmentRepository->find($id);

        if ($appartment === null) {
            return $this->json(['message' => 'Appartment not found.'], Response::HTTP_NOT_FOUND);
        }

        $appartment
            ->setName($request->name)
            ->setDescription($request->description)
            ->setPrice(new Money($request->priceAmount, Currency::from($request->priceCurrency)))
            ->setCleaningFee(new Money($request->cleaningFeeAmount, Currency::from($request->cleaningFeeCurrency)))
            ->setLastBookedOnUtc($request->lastBookedOnUtc !== null ? new DateTimeImmutable($request->lastBookedOnUtc) : null)
            ->setAmenities(array_map(static fn (int $amenity): Amenity => Amenity::from($amenity), $request->amenities))
            ->setAddress(new Address($request->street, $request->streetNumber, $request->zipcode, $request->city));

        $this->appartmentRepository->save($appartment, true);

        return $this->json($this->normalizeAppartment($appartment));
    }

    #[Route('/api/apartment/{id}', name: 'api_apartment_delete', methods: ['DELETE'])]
    #[OA\Delete(summary: 'Delete apartment', description: 'Deletes an apartment by id.')]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Apartment id', schema: new OA\Schema(type: 'integer', minimum: 1))]
    #[OA\Response(response: 204, description: 'Apartment deleted')]
    #[OA\Response(
        response: 404,
        description: 'Apartment not found',
        content: new OA\JsonContent(ref: new Model(type: ApiMessageResponse::class))
    )]
    #[OA\Tag(name: 'Apartment')]
    public function deleteAppartment(int $id): JsonResponse
    {
        $appartment = $this->appartmentRepository->find($id);

        if ($appartment === null) {
            return $this->json(['message' => 'Appartment not found.'], Response::HTTP_NOT_FOUND);
        }

        $this->appartmentRepository->remove($appartment, true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeAppartment(Appartment $appartment): array
    {
        $address = $appartment->getAddress();

        return [
            'id' => $appartment->getId(),
            'name' => $appartment->getName(),
            'description' => $appartment->getDescription(),
            'price' => [
                'amount' => $appartment->getPrice()->amount,
                'currency' => $appartment->getPrice()->currency->value,
            ],
            'cleaningFee' => [
                'amount' => $appartment->getCleaningFee()->amount,
                'currency' => $appartment->getCleaningFee()->currency->value,
            ],
            'lastBookedOnUtc' => $appartment->getLastBookedOnUtc()?->format(DateTimeInterface::ATOM),
            'amenities' => array_map(
                static fn (Amenity $amenity): array => [
                    'name' => $amenity->name,
                    'value' => $amenity->value,
                ],
                $appartment->getAmenities(),
            ),
            'address' => [
                'street' => $address->street,
                'streetNumber' => $address->streetNumber,
                'zipcode' => $address->zipcode,
                'city' => $address->city,
                'full' => $address->__toString(),
            ],
        ];
    }
}
