<?php

namespace App\Repository;

use App\Entity\News;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    /**
     * @param News $news
     * @return News[] Returns an array of News objects
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByTags(News $news)
    {
        $conn = $this->getEntityManager()->getConnection();
        $tags = $news->getTags();

        if (count($tags) === 0) {
            return [];
        }

        $ids = [];
        foreach ($tags as $tag) {
            $ids[] = $tag->getId();
        }

        $sql = 'SELECT n.id
                FROM news n 
                INNER JOIN tag_news tn ON tn.news_id = n.id 
                WHERE tn.tag_id IN (' . implode(',', $ids) .') 
                AND n.id != :id
                GROUP BY n.id
                ORDER BY (SELECT COUNT(*)
                    FROM tag_news tn2 
                    WHERE tn2.news_id = n.id
                    AND tn2.tag_id IN (
                        SELECT tag_id 
                        FROM tag_news tn3 
                        WHERE tn3.news_id = :id
                    ) )  DESC,
                n.created_at DESC
                LIMIT 5';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('id', $news->getId());
        $stmt->execute();
        $result = $stmt->fetchAll(FetchMode::COLUMN);

        $qb = $this->createQueryBuilder('n')
            ->where('n.id IN (:ids)')
            ->setParameter('ids', $result)
            ->getQuery();



        return $qb->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findLatest():QueryBuilder
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.createdAt', 'desc')

        ;
    }
}
