<?php

namespace App\Entity;

use App\Repository\NotificationUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationUserRepository::class)
 */
class NotificationUser
{
    

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notificationUsers")
     */
    private $user;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=Notification::class, inversedBy="notificationUsers",cascade={"persist", "remove"})
     */
    private $notification;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $seen;

   public function __construct()
   {
       $this->seen=false;
   }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(?Notification $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    public function getSeen(): ?bool
    {
        return $this->seen;
    }

    public function setSeen(?bool $seen): self
    {
        $this->seen = $seen;

        return $this;
    }
}
