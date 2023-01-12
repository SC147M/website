<?php

namespace App\Repository;

use App\Entity\SnookerBreak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;

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
    public function findByUser($userId)
    {
        return $this->createQueryBuilder('sb')
            ->andWhere('sb.score >= 30')
            ->setMaxResults(10)
            ->orderBy('sb.user', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * @return SnookerBreak[] Returns an array of SnookerBreak objects
     */
    public function findLatestHighBreaks()
    {
        return $this->createQueryBuilder('sb')
            ->andWhere('sb.score >= 30')
            ->orderBy('sb.createdAt', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $months
     * @return SnookerBreak[] Returns an array of SnookerBreak objects
     */
    public function findHighestBreaks($months = 3)
    {
        $date = new \DateTime('-' . $months . ' months');

        return $this->createQueryBuilder('sb')
            ->andWhere('sb.createdAt >= :date')

            ->setMaxResults(20)
            ->orderBy('sb.score', 'DESC')
            ->setParameter('date', $date)

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
