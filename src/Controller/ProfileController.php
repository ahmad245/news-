<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountEditType;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
   
    /**
     * @Route("/profile", name="my_profile")
     */
    public function index(UserRepository $repoUser, NewsRepository $repoNews)
    {
        $user = $this->getUser();
        return $this->render('profile/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/profile/myNews", name="my_profile_news")
     */
    public function myNews(UserRepository $repoUser, NewsRepository $repoNews)
    {
        $allNews = $repoNews->findMyNews($this->getUser());
        return $this->render('profile/myNews.html.twig', [
            'allNews' => $allNews
        ]);
    }

     /**
     * @Route("/profile/edit", name="my_profile_edit")
     */
    public function edit(Request $req,EntityManagerInterface $em){
        $user=$this->getUser();
        $form=$this->createForm(AccountEditType::class,$user);
        $form->handleRequest($req);
        if ($form->isSubmitted()  ) {
           
           // $this->em->persist($user);
           $em->flush();
            
            return $this->redirectToRoute('account_login');
        }
        return $this->render('profile/edit.html.twig',[
            'form'=>$form->createView()
        ]);
            
    }

    

  
}
