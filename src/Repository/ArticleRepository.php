<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // Create a custome query method
    // with doctrine we work with object and his properties
    
    // The query logic:
    // Create a first private methode that return a qb var with the andWhere clause
    private function addIsPublishedQueryBuilder(QueryBuilder $qb = null){
        return $this->getOrCreateQueryBuilder($qb)
            ->andWhere('a.publishedAt IS NOT NULL');
    }
    // Create a second private methode
    private function getOrCreateQueryBuilder(QueryBuilder $qb = null){
        // if $qb is true return it or create a new queryBuilder
        // Then the addIsPublishedQueryBuilder argument could be optional
        return $qb ?: $this->createQueryBuilder('a');
    }
   /**
    * @return Article[] Returns an array of Article objects
    */
    public function findAllPublishedOrderedByNewest()
    {
        // re-use the createNonDeletedCriteria query
         $this->createQueryBuilder('a')
            ->addCriteria(CommentRepository::createNonDeletedCriteria());

        return $this->addIsPublishedQueryBuilder()
            ->leftJoin('a.tags', 't')
            ->addSelect('t')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
