<?php

declare(strict_types=1);

namespace App\Layers\Domain\Appartment\Entity;

use App\Common\Doctrine\NonPersistedEntityException;
use App\Entity\common\Entity as BaseEntity;
use App\Entity\common\SetTimestampTrait;
use App\Layers\Domain\Appartment\Entity\Embeddable\Address;
use App\Layers\Domain\Appartment\Enum\Amenity;
use App\Layers\Domain\Appartment\Money;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AppartmentRepository::class)]
#[ORM\Table(name: 'appartment')]
#[ORM\HasLifecycleCallbacks]
final class Appartment extends BaseEntity
{
    use SetTimestampTrait;

    #[ORM\Column(length: 64)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'price_')]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Money $price;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'cleaning_fee_')]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Money $cleaningFee;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $lastBookedOnUtc = null;

    /** @var list<Amenity|int> */
    #[ORM\Column(type: 'json')]
    private array $amenities;

    #[ORM\Embedded(class: Address::class)]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Address $address;

    /**
     * @param list<Amenity|int> $amenities
     */
    public function __construct(string $name, ?string $description, Money $price, Money $cleaningFee, ?DateTimeImmutable $lastBookedOnUtc, array $amenities, Address $address)
    {
        parent::__construct();
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->cleaningFee = $cleaningFee;
        $this->lastBookedOnUtc = $lastBookedOnUtc;
        $this->amenities = $this->normalizeAmenities($amenities);
        $this->address = $address;
        $this->setTimestampsToNow();
    }

    public function getId(): int
    {
        if (!isset($this->id)) {
            throw NonPersistedEntityException::NonPersistedEntityException();
        }

        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function setPrice(Money $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCleaningFee(): Money
    {
        return $this->cleaningFee;
    }

    public function setCleaningFee(Money $cleaningFee): static
    {
        $this->cleaningFee = $cleaningFee;

        return $this;
    }

    public function getLastBookedOnUtc(): ?DateTimeImmutable
    {
        return $this->lastBookedOnUtc;
    }

    public function setLastBookedOnUtc(?DateTimeImmutable $lastBookedOnUtc): static
    {
        $this->lastBookedOnUtc = $lastBookedOnUtc;

        return $this;
    }

    /**
     * @return list<Amenity>
     */
    public function getAmenities(): array
    {
        return $this->normalizeAmenities($this->amenities);
    }

    /**
     * @param list<Amenity|int> $amenities
     */
    public function setAmenities(array $amenities): static
    {
        $this->amenities = $this->normalizeAmenities($amenities);

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @param list<Amenity|int> $amenities
     *
     * @return list<Amenity>
     */
    private function normalizeAmenities(array $amenities): array
    {
        return array_map(
            static fn (Amenity|int $amenity): Amenity => $amenity instanceof Amenity ? $amenity : Amenity::from($amenity),
            $amenities,
        );
    }
}
