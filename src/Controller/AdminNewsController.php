<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Entity\Attachment;
use App\Repository\NewsRepository;
use App\Services\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class AdminNewsController extends AbstractController
{
    private $em;
    private $repoNews;
    public function __construct(EntityManagerInterface $em,NewsRepository $repoNews)
    {

        $this->em = $em;
        $this->repoNews=$repoNews;
       
    }
    /**
     * @Route("/admin/news/{page}", name="admin_news", requirements={"page":"\d+"})
     *  @Security("is_granted('ROLE_ADMIN')")
     */
    public function index($page=1,PaginationService $pagination)
    {
        $limit=10;
        $offset= ($page * $limit) -$limit ;
       
        $data=$this->repoNews->filter(null,$limit, $offset);
        //dd($data);die;
        $total=$this->repoNews->countFillter(null,$limit,$offset);
        $pagination->setEntityClass(News::class)->setData($data)->setLimit($limit)->setPage($page)->setTotal($total);


        // $limit=10;
        // $offset= ($page * $limit) -$limit ;
        // $total=$this->repoNews->countFillter(null,$limit,$offset);
        // $pagination->setEntityClass(News::class)->setLimit($limit)->setPage($page);
       // $allNews=$this->repoNews->findAll();
        return $this->render('admin/admin_news/index.html.twig', [
         //   'allNews' => $allNews,
            'pagination'=>$pagination,
            'total'=>$total
        ]);
    }

    
    
}
