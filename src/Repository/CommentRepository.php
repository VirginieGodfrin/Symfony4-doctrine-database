<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public static function createNonDeletedCriteria(): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('isDeleted', false))
            ->orderBy(['createdAt' => 'DESC'])
        ;
    }
    // remeber LIKE (Doctrine_dql tuto)
    /**
     * @param string|null $term
     */
    public function getWithSearchQueryBuilder(?string $term): QueryBuilder
    {
        // innerjoin beacause comment is owner
        // With addSelect('a'), we're telling the QueryBuilder to select 
        // all of the comment columns and all of the article columns.
        $qb = $this->createQueryBuilder('c') 
            ->innerJoin('c.article', 'a')
            ->addSelect('a');

        if ($term) {
            $qb->andWhere('c.content LIKE :term OR c.authorName LIKE :term OR a.title LIKE :term')
                ->setParameter('term', '%' . $term . '%')
            ;
        }

        // with paginator we need a query not an array of result
        return $qb
            ->orderBy('c.createdAt', 'DESC');

    }


    public function commentsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC');
        ;
    }


    // ccl: if your page has a lot of queries because Doctrine is making extra queries across a relationship, 
    // just join over that relationship and use addSelect() to fetch all the data you need at once.
    // But... there is one confusing thing about this. We're now selecting all of the comment data and all of the article data. 
    // But... you'll notice, the page still works! What I mean is, even though we're suddenly selecting more data, 
    // our findAllWithSearch() method still returns exactly what it did before: 
    // it returns a array of Comment objects. It does not, for example, now return Comment and Article objects.
    // Instead, Doctrine takes that extra article data and stores it in the background for later. 
    // But, the new addSelect() does not affect the return value. That's way different than using raw SQL.

}
