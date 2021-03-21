<?php

namespace App\Repository;

use App\Entity\Ranking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ranking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ranking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ranking[]    findAll()
 * @method Ranking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RankingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ranking::class);
    }

    /**
     * @param int $min
     * @param int $max
     * @return Ranking[] Returns an array of Ranking objects
     */
    public function findByInRange(int $min, int $max)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.position >= :min')
            ->andWhere('r.position <= :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->orderBy('r.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $position
     * @return mixed
     */
    public function increasePositions(int $position)
    {
        return $this->createQueryBuilder('r')
            ->update()
            ->set('r.position', 'r.position+1')
            ->where('r.position >= :position')
            ->setParameter('position', $position)
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $startPosition
     * @param int $endPosition
     * @return mixed
     */
    public function increasePositionsFromTo(int $startPosition, int $endPosition)
    {
        return $this->createQueryBuilder('r')
            ->update()
            ->set('r.position', 'r.position+1')
            ->where('r.position >= :startPosition')
            ->andWhere('r.position <= :endPosition')
            ->setParameter('startPosition', $startPosition)
            ->setParameter('endPosition', $endPosition)
            ->getQuery()
            ->execute();
    }
}
