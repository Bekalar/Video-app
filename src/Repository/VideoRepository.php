<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Video>
 *
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Video::class);
    }

    public function findByChildIds(array $value, int $page, PaginatorInterface $paginator, ?string $sort_method)
    {
        $sort_method = $sort_method != 'rating' ? $sort_method : 'ASC';
        $dbquery = $this->createQueryBuilder('v')
            ->andWhere('v.category IN (:val)')
            ->setParameter('val', $value)
            ->orderBy('v.title', $sort_method)
            ->getQuery();
        $pagination = $paginator->paginate($dbquery, $page, 5);
        return $pagination;
    }

    public function findByTitle(string $query, int $page, PaginatorInterface $paginator, ?string $sort_method,)
    {
        $sort_method = $sort_method != 'rating' ? $sort_method : 'ASC';

        $querybuilder = $this->createQueryBuilder('v');
        $searchTerms = $this->prepareQuery($query);
        // $searchTerms = $this->getEntityManager()->getConnection();
        // $values = $searchTerms->prepare($query);

        foreach ($searchTerms as $key => $term) {
            $querybuilder
                ->orWhere('v.title LIKE :t_' . $key)
                ->setParameter('t_' . $key, '%' . trim($term) . '%');
        }

        $dbquery = $querybuilder
            ->addOrderBy('v.title', $sort_method)
            ->getQuery();

        return $paginator->paginate($dbquery, $page, 5);
    }

    private function prepareQuery(string $query): array
    {
        return explode(' ', $query);
    }

    public function videoDetails($id)
    {
        return $this->createQueryBuilder('v')
        ->leftJoin('v.comments', 'c')
        ->leftJoin('c.user', 'u')
        ->addSelect('c', 'u')
        ->where('v.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();
    }

    //    /**
    //     * @return Video[] Returns an array of Video objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Video
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
