<?php

namespace App\Repository;

use App\Entity\Label;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @method Label|null find($id, $lockMode = null, $lockVersion = null)
 * @method Label|null findOneBy(array $criteria, array $orderBy = null)
 * @method Label[]    findAll()
 * @method Label[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LabelRepository extends ServiceEntityRepository
{
    public function __construct(
        private ManagerRegistry $registry,
        private PaginatorInterface $paginator) 
    {
        parent::__construct($registry, Label::class);
    }

    public function getLabelsByParams(array $params) {

        extract($params);

        $page = $page ?? 1;
        $size = $size ?? 30;


        $entityManager = $this->getEntityManager();

        $qb = $this->createQueryBuilder('l');

        if(isset($query) && $query != "") {
            $qb->where('LOWER(l.name) LIKE :query');
            $qb->setParameter('query', '%'.strtolower($query).'%');
        }

        $query = $qb->getQuery();

        $pagination = $this->paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            $size /*limit per page*/
        );
        return $pagination;
    }

    // /**
    //  * @return Label[] Returns an array of Label objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Label
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
