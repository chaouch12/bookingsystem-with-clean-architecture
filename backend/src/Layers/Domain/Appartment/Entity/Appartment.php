<?php

declare(strict_types=1);

namespace App\Layers\Domain\Appartment\Entity;

use App\Entity\common\SetTimestampTrait;
use App\Layers\Domain\Appartment\Enum\Amenity;
use App\Repository\AppartmentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppartmentRepository::class)]
#[ORM\Table(name: 'appartment')]
#[ORM\HasLifecycleCallbacks]
class Appartment
{
    use SetTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(
        options: [
            'unsigned' => true,
        ]
    )]
    private int $id;

    #[ORM\Column(length: 64)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private string $priceCurrency;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private string $cleaningFeeAmount;

    #[ORM\Column(length: 20)]
    private string $cleaningFeeCurrency;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $lastBookedOnUtc = null;

    #[ORM\Column(type: 'json')]
    /** @var Amenity[] $amenities */
    private array $amenities;

    /**
     * @param int|null $id
     * @param string $name
     * @param string|null $description
     * @param string $priceCurrency
     * @param string $cleaningFeeAmount
     * @param string $cleaningFeeCurrency
     * @param DateTimeImmutable|null $lastBookedOnUtc
     * @param Amenity[] $amenities
     */
    public function __construct(?int $id, string $name, ?string $description, string $priceCurrency, string $cleaningFeeAmount, string $cleaningFeeCurrency, ?DateTimeImmutable $lastBookedOnUtc, array $amenities)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->priceCurrency = $priceCurrency;
        $this->cleaningFeeAmount = $cleaningFeeAmount;
        $this->cleaningFeeCurrency = $cleaningFeeCurrency;
        $this->lastBookedOnUtc = $lastBookedOnUtc;
        $this->amenities = $amenities;
        $this->setTimestampsToNow();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getPriceCurrency(): ?string
    {
        return $this->priceCurrency;
    }

    public function setPriceCurrency(string $priceCurrency): static
    {
        $this->priceCurrency = $priceCurrency;

        return $this;
    }

    public function getCleaningFeeAmount(): ?string
    {
        return $this->cleaningFeeAmount;
    }

    public function setCleaningFeeAmount(string $cleaningFeeAmount): static
    {
        $this->cleaningFeeAmount = $cleaningFeeAmount;

        return $this;
    }

    public function getCleaningFeeCurrency(): ?string
    {
        return $this->cleaningFeeCurrency;
    }

    public function setCleaningFeeCurrency(string $cleaningFeeCurrency): static
    {
        $this->cleaningFeeCurrency = $cleaningFeeCurrency;

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
}
