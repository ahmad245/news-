<?php

namespace App\EventListener;

use App\Entity\Comment;
use App\Entity\CommentNotification;
use App\Entity\FollowingNotification;
use App\Entity\LikeNotification;
use App\Entity\News;
use App\Entity\NewsNotification;
use App\Entity\NotificationUser;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;


class NewsSubscriber implements EventSubscriber
{

    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }
    public function onFlush(OnFlushEventArgs $args)
    {

        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        $classNU = $em->getClassMetadata(NotificationUser::class);

        foreach ($uow->getScheduledEntityInsertions() as $entityYpdate) {

            if ($entityYpdate instanceof News) {
                $onwer = $entityYpdate;
                $notificationNews = new NewsNotification();
                


                $notificationNews->setNews($onwer);
                $notificationNews->setCreatedBy($onwer->getUser());


               
                $em->persist($notificationNews);
                foreach ($em->getRepository(User::class)->findAll()    as $user) {
                    $notificationUser = new NotificationUser();
                    $notificationUser->setUser($user);
                    $notificationUser->setNotification($notificationNews);

                    $notificationNews->addNotificationUser($notificationUser);
                }

     
                $uow->computeChangeSet($em->getClassMetadata(NewsNotification::class), $notificationNews);
            }
        }
    }
}

