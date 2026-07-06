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
use DateTimeImmutable;
use DateTimeInterface;
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
    public function getAppartment(int $id): JsonResponse
    {
        $appartment = $this->appartmentRepository->find($id);

        if ($appartment === null) {
            return $this->json(['message' => 'Appartment not found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->normalizeAppartment($appartment));
    }

    #[Route('/api/apartment/{id}', name: 'api_apartment_update', methods: ['PUT'])]
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
