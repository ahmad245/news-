<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Notification;
use App\Entity\NotificationUser;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    // /**
    //  * @return Notification[] Returns an array of Notification objects
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
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getCountNotification(User $user){
        return $this->createQueryBuilder('n')
                    ->select('count(n)')
                    ->join('n.notificationUsers','nu')
                    ->where('nu.user = :user')

                    ->andWhere('nu.seen = false')
                    ->groupBy('nu.user')
                    ->setParameter('user',$user)
                    ->getQuery()
                    ->getOneOrNullResult()
                   //->getSingleScalarResult()
                    ;
    }

    public function getAllNofification($user){
        return $this->createQueryBuilder('n')
        ->select('nu','n')
        ->join('n.notificationUsers','nu')
        ->where('nu.user = :user')
        ->andWhere('nu.seen = false')
        ->setParameter('user',$user)
        ->getQuery()
        ->getResult()
        ;
    }

    public function updateAllNotificationAssSeen(User $user)
    {
        return $this->createQueryBuilder('n')
                   
                    ->update('App\Entity\NotificationUser','nu')
                    ->where('nu.user = :user')
                    ->set('nu.seen',true)
                    //->where('u = :user')
                    ->setParameter('user',$user)
                    ->getQuery()
                    ->execute();
    }
}
