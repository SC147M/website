<?php

namespace App\Repository;

use App\Entity\SnookerBreak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SnookerBreak|null find($id, $lockMode = null, $lockVersion = null)
 * @method SnookerBreak|null findOneBy(array $criteria, array $orderBy = null)
 * @method SnookerBreak[]    findAll()
 * @method SnookerBreak[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SnookerBreakRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SnookerBreak::class);
    }

    /**
     * @return SnookerBreak[] Returns an array of SnookerBreak objects
     */
    public function findLatestHighBreaks()
    {
        return $this->createQueryBuilder('sb')
            ->andWhere('sb.score >= 25')
            ->orderBy('sb.createdAt', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?SnookerBreak
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
