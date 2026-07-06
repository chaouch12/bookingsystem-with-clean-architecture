<?php

declare(strict_types=1);

namespace App\Layers\Domain\Booking\Repository;

use App\Layers\Domain\Booking\Entity\Booking;
use App\Layers\Domain\Booking\Enum\BookingStatus;
use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
final class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function existsOverlapForAppartment(int $appartmentId, BookingPeriod $period): bool
    {
        $queryBuilder = $this->createQueryBuilder('booking');
        $queryBuilder
            ->select('1')
            ->andWhere('booking.appartmentId = :appartmentId')
            ->andWhere('booking.status IN (:blockingStatuses)')
            ->andWhere('booking.period.checkIn < :checkOut')
            ->andWhere('booking.period.checkOut > :checkIn')
            ->setParameter('appartmentId', $appartmentId)
            ->setParameter('blockingStatuses', [
                BookingStatus::PENDING->value,
                BookingStatus::CONFIRMED->value,
            ])
            ->setParameter('checkIn', $period->checkIn)
            ->setParameter('checkOut', $period->checkOut)
            ->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult() !== null;
    }

    public function save(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
