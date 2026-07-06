<?php

declare(strict_types=1);

namespace App\Layers\Domain\Booking\Entity;

use App\Entity\common\Entity;
use App\Entity\common\SetTimestampTrait;
use App\Layers\Domain\Appartment\Money;
use App\Layers\Domain\Booking\Enum\BookingStatus;
use App\Layers\Domain\Booking\Event\BookingCreated;
use App\Layers\Domain\Booking\Repository\BookingRepository;
use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use App\Layers\Domain\Booking\ValueObject\GuestCount;
use App\Layers\Domain\Shared\Event\RecordsDomainEvents;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'booking')]
#[ORM\HasLifecycleCallbacks]
final class Booking extends Entity
{
    use RecordsDomainEvents;
    use SetTimestampTrait;

    #[ORM\Column(name: 'appartment_id', options: ['unsigned' => true])]
    private int $appartmentId;

    #[ORM\Column(name: 'guest_user_id', options: ['unsigned' => true])]
    private int $guestUserId;

    #[ORM\Embedded(class: BookingPeriod::class, columnPrefix: false)]
    private BookingPeriod $period;

    #[ORM\Embedded(class: GuestCount::class, columnPrefix: false)]
    private GuestCount $guestCount;

    #[ORM\Column(name: 'status', length: 16, enumType: BookingStatus::class)]
    private BookingStatus $status;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'price_for_period_')]
    private Money $priceForPeriod;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'cleaning_fee_')]
    private Money $cleaningFee;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'amenities_up_charge_')]
    private Money $amenitiesUpCharge;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'total_price_')]
    private Money $totalPrice;

    private function __construct(
        int $appartmentId,
        int $guestUserId,
        BookingPeriod $period,
        GuestCount $guestCount,
        Money $priceForPeriod,
        Money $cleaningFee,
        Money $amenitiesUpCharge,
        Money $totalPrice,
    ) {
        parent::__construct();
        $this->appartmentId = $appartmentId;
        $this->guestUserId = $guestUserId;
        $this->period = $period;
        $this->guestCount = $guestCount;
        $this->status = BookingStatus::PENDING;
        $this->priceForPeriod = $priceForPeriod;
        $this->cleaningFee = $cleaningFee;
        $this->amenitiesUpCharge = $amenitiesUpCharge;
        $this->totalPrice = $totalPrice;
        $this->setTimestampsToNow();
    }

    public static function create(
        int $appartmentId,
        int $guestUserId,
        BookingPeriod $period,
        GuestCount $guestCount,
        Money $priceForPeriod,
        Money $cleaningFee,
        Money $amenitiesUpCharge,
        Money $totalPrice,
    ): self {
        $booking = new self($appartmentId, $guestUserId, $period, $guestCount, $priceForPeriod, $cleaningFee, $amenitiesUpCharge, $totalPrice);
        $booking->recordDomainEvent(
            new BookingCreated(
                $booking->getAppartmentId(),
                $booking->getGuestUserId(),
                $booking->getPeriod()->checkIn,
                $booking->getPeriod()->checkOut,
                $booking->getStatus(),
            ),
        );

        return $booking;
    }

    public function getAppartmentId(): int
    {
        return $this->appartmentId;
    }

    public function getGuestUserId(): int
    {
        return $this->guestUserId;
    }

    public function getPeriod(): BookingPeriod
    {
        return $this->period;
    }

    public function getGuestCount(): GuestCount
    {
        return $this->guestCount;
    }

    public function getStatus(): BookingStatus
    {
        return $this->status;
    }

    public function getPriceForPeriod(): Money
    {
        return $this->priceForPeriod;
    }

    public function getCleaningFee(): Money
    {
        return $this->cleaningFee;
    }

    public function getAmenitiesUpCharge(): Money
    {
        return $this->amenitiesUpCharge;
    }

    public function getTotalPrice(): Money
    {
        return $this->totalPrice;
    }
}
