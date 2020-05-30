<?php

namespace App\Controller;

use DateTime;
use App\Entity\News;
use App\Entity\User;
use App\Entity\Filter;
use App\Form\NewsType;
use App\Form\FilterType;
use App\Services\Referer;
use App\Entity\Attachment;
use App\Entity\NewsNotification;
use App\Entity\NotificationUser;
use App\Repository\NewsRepository;
use App\Services\AttachmentManager;
use App\Services\PaginationService;
use App\Repository\AttachmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewsController extends AbstractController
{
    private $em;
    /**
     * @var AttachmentManager
     */
    private $attachmentManager;

    /**
     * @var AttachmentRepository
     */
    private $attachmentRepository;


    public function __construct(EntityManagerInterface $em, AttachmentRepository $attachmentRepository, AttachmentManager $attachmentManager)
    {

        $this->em = $em;
        $this->attachmentManager = $attachmentManager;

        $this->attachmentRepository = $attachmentRepository;
    }


    /**
     * @Route("/news", name="news")
     */
    public function index(NewsRepository $newsRepo, Request $req, PaginationService $pagination)
    {
        $limit=5;
        $filter  = new Filter();
        $form = $this->createForm(FilterType::class, $filter);
        $form->handleRequest($req);
        if ($req->query->get('page')) {
            $filter->page = $req->query->get('page');
        }
     
        if (!empty($form->getData()->startDate)) {
           $timeFormStr= date('Y-m-d', strtotime( str_replace('/', '-', $form->getData()->startDate))); 
           $filter->startDate =new DateTime($timeFormStr);
            
        }
        if (!empty($form->getData()->endDate)) {
        
            $timeFormEnd= date('Y-m-d', strtotime( str_replace('/', '-', $form->getData()->endDate))); 
           
            $filter->endDate = new DateTime($timeFormEnd);
        }

          ///////////
          $offset= ($filter->page * $limit) -$limit ;
          $data=$newsRepo->filter($filter,$limit, $offset);
          //dd($data);die;
          $total=$newsRepo->countFillter($filter,$limit,$offset);
          $pagination->setEntityClass(News::class)->setData($data)->setLimit($limit)->setPage($filter->page)->setTotal($total);
         
          //////////////


        return $this->render('home/index.html.twig', [
            'allNews' => $pagination,
            'form' => $form->createView(),

        ]);
    }
    /**
     * @Route("/news/add/{type}", name="news_add")
     *  @Security("is_granted('ROLE_USER') ")
     * @param Request $req
     * @return void
     */
    public function add(Request $req, string $type = null)
    {

        $news = new News();
        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $regex = '~/uploads/[a-zA-Z0-9]+\.\w+~';
            $regexToReplace = '/^uploads/';

            $file = $form->getData();
            if (preg_match_all($regex, $file->getContent(), $matches)) {

                foreach ($matches[0] as  $match) {

                    $attachment = new Attachment();
                    $attachment->setFileName(str_replace("/uploads/", "", $match));
                    $attachment->setPath($match);
                    $attachment->setNews($news);
                    $this->em->persist($attachment);
                   // dd($attachment);
                }
                //die;
            
            }
            $news->setContent(str_replace(["../../uploads", "../uploads", "../../../uploads"], "/uploads", $file->getContent()));

            foreach ($news->getCategories() as $category) {
                $news->addCategory($category);
            }

            $news->setUser($this->getUser());


            $this->em->persist($news);

            // $this->manageNotification($news,$this->em);

            $this->em->flush();
            if ($type == 'admin') {
                return $this->redirectToRoute('admin_news');
            }

            $this->addFlash(
                'success',
                'l\'actualité a été ajouté !'
            );
            return $this->redirectToRoute('home');
        }

        return $this->render('news/create.html.twig', [
            'type' => $type,
            'news' => $news,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/news/edit/{id}/{type}",name="news_edit")
     * @Security("is_granted('ROLE_ADMIN') or  (is_granted('ROLE_USER') and user==news.getUser())")
     * @param News $news
     * @param Request $req
     * @return void
     */
    public function edit(News $news, Request $req, $type = null)
    {

        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $uow = $this->em->getUnitOfWork();
            $uow->computeChangeSets();
            $changeSet = $uow->getEntityChangeSet($news);

            if (isset($changeSet['content'])) {

                $data = $form->getData();
                $regex = '~/uploads/[a-zA-Z0-9]+\.\w+~';
                $matches = [];

                if (preg_match_all($regex, $data->getContent(), $matches) > 0) {

                    $filenames = array_map(function ($match) {
                        return basename($match);
                    }, $matches[0]);

                    $recordsToRemove = $this->attachmentRepository->findAttachmentsToRemove($filenames, $news->getId());

                    /** @var Attachment $record */
                    foreach ($recordsToRemove as $record) {
                        // remove the record from the db
                        $this->em->remove($record);
                        // remove the file from the server
                        $this->attachmentManager->removeAttachment($record->getFilename());
                    }
                } else if ($news->getAttachments()->count() && $matches) {
                    /** @var Attachment $record */
                    foreach ($news->getAttachments() as $record) {
                        // remove the record from the db
                        $entity = $this->em->merge($record);
                        $this->em->remove($entity);
                        // remove the file from the server
                        $this->attachmentManager->removeAttachment($record->getFilename());
                    }
                }
            }
            $regex = '~/uploads/[a-zA-Z0-9]+\.\w+~';

            $file = $form->getData();
            if (preg_match_all($regex, $file->getContent(), $matches)) {
                foreach ($matches[0] as  $match) {

                    $attachment = new Attachment();
                    $attachment->setFileName(str_replace("/uploads/", "", $match));
                    $attachment->setPath($match);
                    $attachment->setNews($news);
                    $this->em->persist($attachment);
                }
                $news->setContent(str_replace("../../uploads/", "/uploads/", $file->getContent()));
            }
            $this->em->flush();
            $this->addFlash(
                'success',
                'l\'actualité a été modifié !'
            );
            if ($type == 'admin') {
                return $this->redirectToRoute('admin_news');
            }

            return $this->redirectToRoute('home');
        }

        // $this->em->flush();

        return $this->render('news/create.html.twig', [
            'form' => $form->createView(),
            'news' => $news,
            'type' => $type
        ]);
    }
    /**
     * @Route("/news/delete/{id}/{type}",name="news_delete")
     * @Security("is_granted('ROLE_ADMIN') or  (is_granted('ROLE_USER') and user==news.getUser())")
     * @param News $news
     * @return void
     */
    public function delete(News $news, $type = null)
    {
        $filenames = $news->getAttachments();
        if (count($filenames) > 0) {
            foreach ($filenames as $filename) {

                $this->attachmentManager->removeAttachment($filename->getFileName());
            }
        }

        $this->em->remove($news);
        $this->em->flush();
        $this->addFlash(
            'success',
            'l\'actualité a été supprimé !'
        );
        if ($type == 'admin') {
            return $this->redirectToRoute('admin_news');
        }
        return $this->redirectToRoute('home');
    }
    /**
     * @Route("/news/{id}",name="news_show")
     * @param News $news
     * @return void
     */
    public function show(News $news)
    {
        return $this->render('news/show.html.twig', [

            'news' => $news,

        ]);
    }

    private function checkQueryParams(Request $req, Filter $filter)
    {
        if (
            empty($req->query->get('title')) &&
            empty($req->query->get('category')) &&
            empty($req->query->get('startDate')) &&
            empty($req->query->get('user')) &&
            empty($req->query->get('endDate'))
        ) {
            return null;
        }
        if (!empty($req->query->get('title'))) {
            $filter->title = $req->query->get('title');
        }
        if (!empty($req->query->get('category'))) {
            $filter->title = $req->query->get('category');
        }
        if (!empty($req->query->get('category'))) {
            $filter->title = $req->query->get('category');
        }
        if (!empty($req->query->get('user'))) {
            $filter->title = $req->query->get('user');
        }
        if (!empty($req->query->get('startDate'))) {
            $filter->title = $req->query->get('startDate');
        }
        if (!empty($req->query->get('endDate'))) {
            $filter->title = $req->query->get('endDate');
        }

        return $filter;
    }

    // private function manageNotification(News $news,$em)
    // {
    //     $notificationNews = new NewsNotification();



    //     $notificationNews->setNews($news);
    //     $notificationNews->setCreatedBy($news->getUser());


    //     $em->persist($notificationNews);

    //     foreach ( $em->getRepository(User::class)->findAll()    as $user) {
    //         $notificationUser=new NotificationUser();
    //         $notificationUser->setUser($user);
    //         $notificationUser->setNotification($notificationNews);
    //         $em->persist($notificationUser);

    //     }


    // }
}
