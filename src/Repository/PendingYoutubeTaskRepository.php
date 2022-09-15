<?php

namespace App\Repository;

use App\Entity\PendingYoutubeTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PendingYoutubeTask>
 *
 * @method PendingYoutubeTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method PendingYoutubeTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method PendingYoutubeTask[]    findAll()
 * @method PendingYoutubeTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PendingYoutubeTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PendingYoutubeTask::class);
    }

    public function add(PendingYoutubeTask $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PendingYoutubeTask $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PendingYoutubeTask[] Returns an array of PendingYoutubeTask objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PendingYoutubeTask
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
