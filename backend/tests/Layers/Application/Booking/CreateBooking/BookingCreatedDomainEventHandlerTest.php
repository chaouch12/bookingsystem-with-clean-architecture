<?php

declare(strict_types=1);

namespace App\Tests\Layers\Application\Booking\CreateBooking;

use App\Layers\Application\Booking\CreateBooking\BookingCreatedDomainEventHandler;
use App\Layers\Application\Shared\Notification\EmailService;
use App\Layers\Domain\Appartment\Entity\Appartment;
use App\Layers\Domain\Appartment\Entity\Embeddable\Address;
use App\Layers\Domain\Appartment\Enum\Amenity;
use App\Layers\Domain\Appartment\Enum\Currency;
use App\Layers\Domain\Appartment\Money;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use App\Layers\Domain\Booking\Enum\BookingStatus;
use App\Layers\Domain\Booking\Event\BookingCreated;
use App\Layers\Domain\Users\Enum\UserRoleType;
use App\Layers\Domain\Users\Enum\UserStatusType;
use App\Layers\Domain\Users\Repository\UserRepository;
use App\Layers\Domain\Users\User;
use App\Layers\Domain\Users\ValueObject\Email;
use App\Layers\Domain\Users\ValueObject\HashedPassword;
use App\Layers\Domain\Users\ValueObject\Name;
use PHPUnit\Framework\TestCase;

final class BookingCreatedDomainEventHandlerTest extends TestCase
{
    public function testItSendsReservationEmailWhenUserAndAppartmentExist(): void
    {
        $user = User::create(
            new HashedPassword('$2y$13$abcdefghijklmnopqrstuvabcdefghijklmnopqrstuv1234567890'),
            UserStatusType::ADMIN,
            UserRoleType::MANAGER,
            new Email('guest@example.com'),
            new Name('Guest User'),
        );
        $user->setId(20);

        $appartment = new Appartment(
            'Mountain Loft',
            null,
            new Money('100.00', Currency::EUR),
            new Money('20.00', Currency::EUR),
            null,
            [Amenity::MountainView],
            new Address('Main Street', '1', '12345', 'Berlin'),
        );

        $collector = new \ArrayObject();
        $emailService = new class($collector) implements EmailService {
            public function __construct(private \ArrayObject $collector)
            {
            }

            public function send(string $email, string $subject, string $body): void
            {
                $this->collector->append([
                    'email' => $email,
                    'subject' => $subject,
                    'body' => $body,
                ]);
            }
        };

        $handler = new BookingCreatedDomainEventHandler(
            $this->createUserRepositoryStub($user),
            $this->createAppartmentRepositoryStub($appartment),
            $emailService,
        );

        $handler->handle(
            new BookingCreated(
                10,
                20,
                new \DateTimeImmutable('2026-08-01'),
                new \DateTimeImmutable('2026-08-05'),
                BookingStatus::RESERVED,
            ),
        );

        self::assertCount(1, $collector);
        self::assertSame('guest@example.com', $collector[0]['email']);
        self::assertSame('Booking reserved!', $collector[0]['subject']);
        self::assertStringContainsString('Mountain Loft', $collector[0]['body']);
        self::assertStringContainsString('2026-08-01', $collector[0]['body']);
        self::assertStringContainsString('2026-08-05', $collector[0]['body']);
    }

    public function testItDoesNothingWhenUserIsMissing(): void
    {
        $collector = new \ArrayObject();
        $emailService = new class($collector) implements EmailService {
            public function __construct(private \ArrayObject $collector)
            {
            }

            public function send(string $email, string $subject, string $body): void
            {
                $this->collector->append([
                    'email' => $email,
                    'subject' => $subject,
                    'body' => $body,
                ]);
            }
        };

        $appartment = new Appartment(
            'Mountain Loft',
            null,
            new Money('100.00', Currency::EUR),
            new Money('20.00', Currency::EUR),
            null,
            [Amenity::MountainView],
            new Address('Main Street', '1', '12345', 'Berlin'),
        );

        $handler = new BookingCreatedDomainEventHandler(
            $this->createUserRepositoryStub(null),
            $this->createAppartmentRepositoryStub($appartment),
            $emailService,
        );

        $handler->handle(
            new BookingCreated(
                10,
                20,
                new \DateTimeImmutable('2026-08-01'),
                new \DateTimeImmutable('2026-08-05'),
                BookingStatus::RESERVED,
            ),
        );

        self::assertCount(0, $collector);
    }

    private function createUserRepositoryStub(?User $user): UserRepository
    {
        return new class($user) extends UserRepository {
            public function __construct(private readonly ?User $user)
            {
            }

            public function find($id, $lockMode = null, $lockVersion = null): ?User
            {
                return $this->user;
            }
        };
    }

    private function createAppartmentRepositoryStub(?Appartment $appartment): AppartmentRepository
    {
        return new class($appartment) extends AppartmentRepository {
            public function __construct(private readonly ?Appartment $appartment)
            {
            }

            public function find($id, $lockMode = null, $lockVersion = null): ?Appartment
            {
                return $this->appartment;
            }
        };
    }
}
