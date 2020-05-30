<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(NewsRepository $newsRepo)
    {
        return $this->redirectToRoute('news');
        // $news=$newsRepo->findAll();
        // return $this->render('home/index.html.twig', [
        //     'allNews' =>$news ,
        // ]);
    }
}
