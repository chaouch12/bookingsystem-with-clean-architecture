<?php

declare(strict_types=1);

namespace App\Layers\Domain\Appartment\Entity;

use App\Common\Doctrine\Exception;
use App\Entity\common\Entity;
use App\Entity\common\SetTimestampTrait;
use App\Layers\Domain\Appartment\Entity\Embeddable\Address;
use App\Layers\Domain\Appartment\Enum\Amenity;
use App\Layers\Domain\Appartment\Money;
use App\Repository\AppartmentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AppartmentRepository::class)]
#[ORM\Table(name: 'appartment')]
#[ORM\HasLifecycleCallbacks]
class Appartment extends Entity
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

    #[ORM\Column(type: 'json')]
    /** @var Amenity[] $amenities */
    private array $amenities;

    #[ORM\Embedded(class: Address::class)]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Address $address;

    /**
     * @param Amenity[] $amenities
     */
    public function __construct(int $id, string $name, ?string $description, Money $price, Money $cleaningFee, ?DateTimeImmutable $lastBookedOnUtc, array $amenities, Address $address)
    {
        parent::__construct($id);
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->cleaningFee = $cleaningFee;
        $this->lastBookedOnUtc = $lastBookedOnUtc;
        $this->amenities = $amenities;
        $this->address = $address;
        $this->setTimestampsToNow();
    }

    public function getId(): int
    {
        if (!isset($this->id)) {
            throw Exception::NonPersistedEntityException();
        }

        return $this->id;
    }

    public function getName(): ?string
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

    public function getAmenities(): array
    {
        return $this->amenities;
    }

    public function setAmenities(array $amenities): static
    {
        $this->amenities = $amenities;

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
}
