<?php

namespace App\Controller;

use App\Services\DashboardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminDashboardController extends AbstractController
{/**
     * @Route("/admin", name="admin_dashboard")
     *  @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(EntityManagerInterface $manager,DashboardService $dashboard)
    {
        
        $users=$dashboard->getUsersCount();
        $news=$dashboard->getNewsCount();
      
        $categories=$dashboard->getCategoryCount();
      
                           
                                       
      
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => compact('users','news','categories')
        ]);
    }
}
