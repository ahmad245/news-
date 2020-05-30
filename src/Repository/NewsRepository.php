<?php

namespace App\Repository;

use App\Entity\Filter;
use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    // /**
    //  * @return News[] Returns an array of News objects
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
    public function findAllNews()
    {
        return $this->createQueryBuilder('n')
            ->select('n','u','c')
            ->join('n.user','u')
            ->join('n.categories','c')
            ->getQuery()
            ->getResult();
           
        ;
    }

    public function getCount($isPublish=true)
    {
        return $this->createQueryBuilder('n')
            ->select('count(n)')
            ->where('n.isPublished = :val')
            ->setParameter('val', $isPublish)
            ->getQuery()
            ->getSingleScalarResult()
           
        ;
    }
    
    public function findAllPublish($isPublish=true)
    {
        return $this->createQueryBuilder('n')
            ->select('n','u','c')
            ->join('n.user','u')
            ->join('n.categories','c')
            ->andWhere('n.isPublished = :val')
            ->setParameter('val', $isPublish)
            ->orderBy('n.createdAt','DESC')
            ->getQuery()
            ->getResult();
           
        ;
    }
    public function findAllPublishPagination($limit=20,$offset=1,$isPublish=true)
    {
        return $this->createQueryBuilder('n')
            ->select('n','u','c')
            ->join('n.user','u')
            ->join('n.categories','c')
            ->andWhere('n.isPublished = :val')
            ->setParameter('val', $isPublish)
            ->orderBy('n.createdAt','DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
           
        ;
    }

    public function findMyNews($user)
    {
        return $this->createQueryBuilder('n')
                    ->select('n','u','c')
                    ->join('n.user','u')
                    ->join('n.categories','c')
                    ->where('u = :user')
                    ->setParameter('user',$user)
                    ->orderBy('n.createdAt','DESC')
                    ->getQuery()
            ->getResult();

    }
    public function findByUser($user,$limit=20,$offset=1)
    {
        return $this->createQueryBuilder('n')
                    ->select('n','u','c')
                    ->join('n.user','u')
                    ->join('n.categories','c')
                    ->where('u.id = :user')
                    ->setParameter('user',$user)
                    ->orderBy('n.createdAt','DESC')
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();

    }

    public function countByUser($user)
    {
        return $this->createQueryBuilder('n')
                    ->select('count(n)')
                    ->join('n.user','u')
                    ->join('n.categories','c')
                    ->where('u.id = :user')
                    ->setParameter('user',$user)
                    ->getQuery()
                    ->getSingleScalarResult();

    }


    public function findByCategory($category,$limit=20,$offset=1)
    {
        return $this->createQueryBuilder('n')
                    ->select('n','u','c')
                    ->join('n.user','u')
                    ->join('n.categories','c')
                    ->where('c.id = :category')
                    ->setParameter('category',$category)
                    ->orderBy('n.createdAt','DESC')
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();

    }
    public function countByCategory($category)
    {
        return $this->createQueryBuilder('n')
                    ->select('count(n)')
                    ->join('n.user','u')
                    ->join('n.categories','c')
                    ->where('c.id = :category')
                    ->setParameter('category',$category)
                    ->getQuery()
                    ->getSingleScalarResult();

    }


    public function findBydate($date,$limit=20,$offset=1)
    {
        return $this->createQueryBuilder('n')
                    ->select('n','u','c')
                    ->join('n.user','u')
                    ->join('n.categories','c')
                    ->where('YEAR(n.createdAt) = :year')
                    ->andWhere('MONTH(n.createdAt) = :month')
                    ->andWhere('DAY(n.createdAt) = :day')
                    ->setParameter('year',$date[0])
                    ->setParameter('month',$date[1])
                    ->setParameter('day',$date[2])
                    // ->orderBy('n.createdAt','DESC')
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                   ->getResult();

    }
    public function countBydate($date)
    {
        return $this->createQueryBuilder('n')
                    ->select('count(n)')
                    ->join('n.user','u')
                    ->join('n.categories','c')
                    ->where('YEAR(n.createdAt) = :year')
                    ->andWhere('MONTH(n.createdAt) = :month')
                    ->andWhere('DAY(n.createdAt) = :day')
                    ->setParameter('year',$date[0])
                    ->setParameter('month',$date[1])
                    ->setParameter('day',$date[2])
                    // ->orderBy('n.createdAt','DESC')
                    ->getQuery()
                    ->getSingleScalarResult();

    }

   
  
    public function searchByTitle($title){
        return $this->createQueryBuilder('n')
               ->where('n.title LIKE :title')
               ->setParameter('title',"%{$title}%") 
               ->getQuery()
               ->getResult();

    }

    public function filter(Filter $filter=null,$limit=20,$offset=1){
       
        $query= $this->createQueryBuilder('n')
        ->select('n','u')
        ->join('n.user','u')
       //->leftJoin('n.attachments','a','WITH','a.news = n')
        //->leftJoin('n.categories','c')
        ->where('n.isPublished = :val')
     
        ->setParameter('val',true);

        if(!empty($filter->title)){
            $query->andWhere('n.title LIKE :title')
            ->setParameter('title',"%{$filter->title}%") ;
        }
        if(!empty($filter->category)){
            $query->join('n.categories','c')
            ->andWhere('c = :category')
            ->setParameter('category',$filter->category) ;
        }
        if(!empty($filter->user)){
            $query->andWhere('u = :user')
            ->setParameter('user',$filter->user) ;
        }

        if(!empty($filter->startDate) && !empty($filter->endDate)){
            $query->andWhere('n.createdAt BETWEEN :startDate AND :endDate ')
            ->setParameter('startDate',$filter->startDate)
            ->setParameter('endDate',$filter->endDate);
        }
         $query->orderBy('n.createdAt','DESC')
         ->setFirstResult($offset)
         ->setMaxResults($limit)
         
     ;  
       // dd($query->getQuery()->getResult(),$limit,$offset);die;
        return $query->getQuery()->getResult();

    }

    public function countFillter(Filter $filter=null,$limit=20,$offset=1){
        $query= $this->createQueryBuilder('n')
        ->select('count(n)')
        ->join('n.user','u')
        // 
        ->where('n.isPublished = :val')
        ->setParameter('val',true);

        if(!empty($filter->title)){
            $query->andWhere('n.title LIKE :title')
            ->setParameter('title',"%{$filter->title}%") ;
        }
        if(!empty($filter->category)){
            $query->join('n.categories','c')
            ->andWhere('c = :category')
            ->setParameter('category',$filter->category) ;
        }
        if(!empty($filter->user)){
            $query->andWhere('u = :user')
            ->setParameter('user',$filter->user) ;
        }

        if(!empty($filter->startDate) && !empty($filter->endDate)){
            $query->andWhere('n.createdAt BETWEEN :startDate AND :endDate ')
            ->setParameter('startDate',$filter->startDate)
            ->setParameter('endDate',$filter->endDate);
        }
        return $query
       
        ->getQuery()
        ->getSingleScalarResult();
    }
    
}
