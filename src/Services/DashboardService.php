<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class DashboardService
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    public function getUsersCount()
    {
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User  as u ')->getSingleScalarResult();
    }
    public function getNewsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\News  as a ')->getSingleScalarResult();
    }
    public function getCategoryCount()
    {
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Category  as b ')->getSingleScalarResult();
    }
  
  
   
}