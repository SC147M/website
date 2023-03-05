<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Table;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return Collection|Reservation[]
     */
    public function findByRange(DateTime $start, DateTime $end)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.end > :start')
            ->andWhere('r.start < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('r.start', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int|array $types
     * @param DateTime  $start
     * @param DateTime  $end
     * @return Collection|Reservation[]
     */
    public function findByTypes($types, DateTime $start, DateTime $end = NULL)
    {
        if (!is_array($types)) {
            $types = [$types];
        }
        if (is_null($end)) {
            $end = new DateTime('9999-12-31 23:59:59');
        }
        const OUTWARDS_TABLE = 4;

        return $this->createQueryBuilder('r')
            ->andWhere('r.end > :start')
            ->andWhere('r.start < :end')
            ->andWhere('r.type IN(:types)')
            ->andWhere(':outwards NOT IN(r.tables)')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('types', $types)
            ->setParameter('outwards', OUTWARDS_TABLE)
            ->orderBy('r.start', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $args
     * @return mixed
     */
    public function getReservationsForValidate(array $args)
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.tables', 't')
            ->andWhere('r.end > :start')
            ->andWhere('r.start < :end')
            ->setParameter('start', $args['start'])
            ->setParameter('end', $args['end']);

        if (!empty($args['id'])) {
            $queryBuilder
                ->setParameter('id', $args['id'])
                ->andWhere('r.id != :id');
        }

        $reservations = $queryBuilder
            ->getQuery()
            ->getResult();

        $requestedTableIds = [];
        /** @var Table $table */
        foreach ($args['tables'] as $table) {
            $requestedTableIds[] = $table->getId();
        }

        $count = [];
        /** @var Reservation $reservation */
        foreach ($reservations as $reservation) {

            $tables = $reservation->getTables();
            foreach ($tables as $table) {
                if (in_array($table->getId(), $requestedTableIds)) {
                    $count[] = $table;
                }
            }
        }

        return $count;
    }
}
