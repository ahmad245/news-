<?php

namespace App\Entity;

use App\Entity\News;
use App\Entity\Notification;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\NewsNotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=NewsNotificationRepository::class)
 */
class NewsNotification extends Notification
{

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="newsNotifications")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=News::class,cascade={"persist", "remove"},inversedBy="newsNotifications")
     * @ORM\JoinColumn(nullable=true)
     */
    private $news;


 

    // public function __construct()
    // {
    //     parent::__construct();
    
    // }



    // public function getId(): ?int
    // {
    //     return $this->id;
    // }

  

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
    public function getNews(): ?News
    {
        return $this->news;
    }

    public function setNews(?News $news): self
    {
        $this->news = $news;

        return $this;
    }


    
    
}
