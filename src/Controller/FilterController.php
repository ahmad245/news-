<?php

namespace App\Controller;

use DateTime;
use App\Entity\News;
use App\Entity\Filter;
use App\Form\FilterType;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use App\Services\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FilterController extends AbstractController
{
    private $repoNews;
    private $repoUser;
    public function __construct(UserRepository $repoUser,NewsRepository $repoNews) {
        $this->repoNews=$repoNews;
        $this->repoUser=$repoUser;
    }
    /**
     * @Route("/filter/user/{id}", name="filter_by_user")
     */
    public function findByUser($id,Request $req,PaginationService $pagination)
    {
        $limit=5;
        $filter  = new Filter();
        $form = $this->createForm(FilterType::class, $filter);
        $form->handleRequest($req);

        if (!empty($form->getData()->startDate)) {
            $timeFormStr= date('Y-m-d', strtotime( str_replace('/', '-', $form->getData()->startDate))); 
            $filter->startDate =new DateTime($timeFormStr);
             
         }
         if (!empty($form->getData()->endDate)) {
         
             $timeFormEnd= date('Y-m-d', strtotime( str_replace('/', '-', $form->getData()->endDate))); 
            
             $filter->endDate = new DateTime($timeFormEnd);
         }
/////////////////////////////////////////
        if ($req->query->get('page')) {
            $filter->page = $req->query->get('page');
        }

        $offset= $filter->page * $limit - $limit ;
       
        
        if ($form->isSubmitted() && $form->isValid()) {
           
        

          $data=$this->repoNews->filter($filter,$limit, $offset);
          $total=$this->repoNews->countFillter($filter,$limit,$offset);
          $pagination->setEntityClass(News::class)->setLimit($limit)->setPage($filter->page)->setTotal($total)->setData($data);
        } 
         else{
          $data=$this->repoNews->findByUser($id,$limit, $offset);
          $total=$this->repoNews->countByUser($id);
          $pagination->setEntityClass(News::class)->setLimit($limit)->setPage($filter->page)->setTotal($total)->setData($data);
         }
           return $this->render('home/index.html.twig', [
            'allNews' =>$pagination ,
            'form' => $form->createView()
        ]);
    }

     /**
     * @Route("/filter/category/{id}", name="filter_by_category")
     */
    public function findByCategory($id,Request $req,PaginationService $pagination)
    {
        $limit=5;

        $filter  = new Filter();
        $form = $this->createForm(FilterType::class, $filter);
        $form->handleRequest($req);


        ///////////////////////////
        if ($req->query->get('page')) {
            $filter->page = $req->query->get('page');
        }

        $offset= $filter->page * $limit - $limit ;
       
        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->getData()->startDate)) {
                $timeFormStr= date('Y-m-d', strtotime( str_replace('/', '-', $form->getData()->startDate))); 
                $filter->startDate =new DateTime($timeFormStr);
                 
             }
             if (!empty($form->getData()->endDate)) {
             
                 $timeFormEnd= date('Y-m-d', strtotime( str_replace('/', '-', $form->getData()->endDate))); 
                
                 $filter->endDate = new DateTime($timeFormEnd);
             }
         
            $data=$this->repoNews->filter($filter,$limit, $offset);
            $total=$this->repoNews->countFillter($filter,$limit,$offset);
            $pagination->setEntityClass(News::class)->setLimit($limit)->setPage($filter->page)->setTotal($total)->setData($data);
        } 
         else{
           
            
            $data=$this->repoNews->findByCategory($id,$limit, $offset);
            $total=$this->repoNews->countByCategory($id);
            $pagination->setEntityClass(News::class)->setLimit($limit)->setPage($filter->page)->setTotal($total)->setData($data);
         } 

        //  return $this->forward('App\Controller\NewsController::index',['allNewsfilter'=>$allNews]);
           return $this->render('home/index.html.twig', [
            'allNews' =>$pagination ,
            'form' => $form->createView()
        
        ]);
    }
    /**
     * @Route("/filter/date/{id}", name="filter_by_date")
     */
    public function findByDate($id,Request $req,PaginationService $pagination)
    {
        $limit=5;
        $filter  = new Filter();
        $form = $this->createForm(FilterType::class, $filter);
        $form->handleRequest($req);

         ///////////////////////////
         if ($req->query->get('page')) {
            $filter->page = $req->query->get('page');
        }
        $offset= $filter->page * $limit - $limit ;
        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->getData()->startDate)) {
                $timeFormStr= date('Y-m-d', strtotime( str_replace('/', '-', $form->getData()->startDate))); 
                $filter->startDate =new DateTime($timeFormStr);
                 
             }
             if (!empty($form->getData()->endDate)) {
             
                 $timeFormEnd= date('Y-m-d', strtotime( str_replace('/', '-', $form->getData()->endDate))); 
                
                 $filter->endDate = new DateTime($timeFormEnd);
             }
          
          $data=$this->repoNews->filter($filter,$limit, $offset);
            $total=$this->repoNews->countFillter($filter,$limit,$offset);
            $pagination->setEntityClass(News::class)->setLimit($limit)->setPage($filter->page)->setTotal($total)->setData($data);
        } 
         else{
    
           $dateArray=explode("-",$id);
         

             
           $data=$this->repoNews->findBydate($dateArray,$limit, $offset);
           $total=$this->repoNews->countBydate($dateArray);
           $pagination->setEntityClass(News::class)->setLimit($limit)->setPage($filter->page)->setTotal($total)->setData($data);
         }
           
           return $this->render('home/index.html.twig', [
            'allNews' =>$pagination ,
            'form' => $form->createView()
        ]);
    }

    
    /**
     * @Route("/filter/title/{title}", name="filter_by_title")
     */
    public function findByTitle($title)
    {
        return $this->json($this->repoNews->searchByTitle($title),200,[],['groups'=>['title']]);
    }


}
