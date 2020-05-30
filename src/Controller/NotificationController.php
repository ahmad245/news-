<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\NotificationUser;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use App\Repository\NotificationUserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationController extends AbstractController
{
    
    private $repo;
    private $em;
    public function __construct(NotificationRepository $repo,EntityManagerInterface $em)
    {
        $this->repo = $repo;
        $this->em = $em;
       
    }
     /**
     * @Route("/notification", name="notification")
     */
    public function unreadCount()
    {
        
        //   $user = $this->getUser();
        //  dd( $user);die;
      $notificationCount=$this->repo->getCountNotification($this->getUser());
      $count=$notificationCount ?  $notificationCount : ['1'=>0];
        return $this->json([
            'code' => 200,
            'message' => 'geting',
            'count' => $count
        ]);
    }

    /**
    
     * @Route("/notification/all", name="notification_all")
     */
    public function allNofification()
    {
           $notifications=$this->repo->getAllNofification($this->getUser());
        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications
            //$this->repo->findBy(['user' => $this->getUser(), 'seen' => false])
        ]);
    }

     /**
     * @Route("/seen/{id}" , name="mark_seen")
     *
     * @param Notification $notification
     * @return void
     */
    public function notificationSeen(NotificationUserRepository $repoNotificationUser)
    {  
       $notification=  $repoNotificationUser->findOneBy(['user'=>$this->getUser()]);
       $notification->setSeen(true);
        $this->em->flush();

        return $this->redirectToRoute('notification_all');

    }

    /**
     * @Route("/allseen" , name="mark_all_seen")
     *
     * @param Notification $notification
     * @return void
     */
    public function allNotificationSeen(){
        $this->repo->updateAllNotificationAssSeen($this->getUser());
        $this->em->flush();
        return $this->redirectToRoute('notification_all');
    }
}




