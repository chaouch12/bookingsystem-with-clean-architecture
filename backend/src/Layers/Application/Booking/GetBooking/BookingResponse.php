<?php

declare(strict_types=1);

namespace App\Layers\Application\Booking\GetBooking;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Flat read model for booking queries.
 */
final readonly class BookingResponse
{
    public function __construct(
        #[Assert\Positive]
        public int $id,
        #[Assert\Positive]
        public int $appartmentId,
        #[Assert\Positive]
        public int $guestUserId,
        #[Assert\NotBlank]
        public string $status,
        #[Assert\NotBlank]
        public string $priceForPeriodAmount,
        #[Assert\NotBlank]
        public string $priceForPeriodCurrency,
        #[Assert\NotBlank]
        public string $cleaningFeeAmount,
        #[Assert\NotBlank]
        public string $cleaningFeeCurrency,
        #[Assert\NotBlank]
        public string $amenitiesUpChargeAmount,
        #[Assert\NotBlank]
        public string $amenitiesUpChargeCurrency,
        #[Assert\NotBlank]
        public string $totalPriceAmount,
        #[Assert\NotBlank]
        public string $totalPriceCurrency,
        #[Assert\NotBlank]
        public string $checkIn,
        #[Assert\NotBlank]
        public string $checkOut,
        #[Assert\Positive]
        public int $guestCount,
        public ?string $createdAt,
        public ?string $confirmedOnUtc,
        public ?string $rejectedOnUtc,
        public ?string $cancelledOnUtc,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['appartment_id'],
            (int) $row['guest_user_id'],
            (string) $row['status'],
            (string) $row['price_for_period_amount'],
            (string) $row['price_for_period_currency'],
            (string) $row['cleaning_fee_amount'],
            (string) $row['cleaning_fee_currency'],
            (string) $row['amenities_up_charge_amount'],
            (string) $row['amenities_up_charge_currency'],
            (string) $row['total_price_amount'],
            (string) $row['total_price_currency'],
            (string) $row['check_in'],
            (string) $row['check_out'],
            (int) $row['guest_count'],
            isset($row['created_at']) ? (string) $row['created_at'] : null,
            isset($row['confirmed_on_utc']) ? (string) $row['confirmed_on_utc'] : null,
            isset($row['rejected_on_utc']) ? (string) $row['rejected_on_utc'] : null,
            isset($row['cancelled_on_utc']) ? (string) $row['cancelled_on_utc'] : null,
        );
    }
}
