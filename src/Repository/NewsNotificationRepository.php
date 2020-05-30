<?php

namespace App\Repository;

use App\Entity\NewsNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NewsNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsNotification[]    findAll()
 * @method NewsNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsNotification::class);
    }

    // /**
    //  * @return NewsNotification[] Returns an array of NewsNotification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NewsNotification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
