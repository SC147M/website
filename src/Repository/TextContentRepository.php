<?php

namespace App\Repository;

use App\Entity\TextContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TextContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextContent[]    findAll()
 * @method TextContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextContent::class);
    }

    // /**
    //  * @return TextContent[] Returns an array of TextContent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TextContent
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
