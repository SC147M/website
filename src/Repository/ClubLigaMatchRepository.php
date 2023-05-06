<?php

namespace App\Repository;

use App\Entity\ClubLigaMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ClubLigaMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClubLigaMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClubLigaMatch[]    findAll()
 * @method ClubLigaMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubLigaMatch::class);
    }

    // /**
    //  * @return ClubLigaMatch[] Returns an array of ClubLigaMatch objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ClubLigaMatch
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
