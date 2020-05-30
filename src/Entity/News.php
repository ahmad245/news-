<?php

namespace App\Entity;

use App\Entity\User;
use App\Listeners\NewsListener;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\NewsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
// @ORM\EntityListeners({"App\Listeners\NewsListener"})
/**
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 *  
 *  @ORM\HasLifecycleCallbacks()
 */
class News
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("title")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="news")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Attachment::class, mappedBy="news",orphanRemoval=true,cascade={"persist", "remove"})
     */
    private $attachments;

     /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $createdAt;

     /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="news" )
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
     /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @ORM\OneToMany(targetEntity=NewsNotification::class, mappedBy="news",orphanRemoval=true,cascade={"persist", "remove"})
     */
    private $newsNotifications;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->isPublished=true;
        $this->newsNotifications = new ArrayCollection();
    }
    /**
     * @ORM\PrePersist
     * @return void
     */
    public function initDateCreate(){
        $this->createdAt=new \DateTime();
    }
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function initDateUpdate(){
        $this->updatedAt=new \DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|Attachment[]
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments[] = $attachment;
            $attachment->setNews($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            // set the owning side to null (unless already changed)
            if ($attachment->getNews() === $this) {
                $attachment->setNews(null);
            }
        }

        return $this;
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



    /**
     * Get the value of isPublished
     */ 
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set the value of isPublished
     *
     * @return  self
     */ 
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get the value of createdAt
     */ 
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */ 
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


     /**
     * @return Collection|NewsNotification[]
     */
    public function getNewsNotifications(): Collection
    {
        return $this->newsNotifications;
    }

    public function addNewsNotification(NewsNotification $newsNotification): self
    {
        if (!$this->newsNotifications->contains($newsNotification)) {
            $this->newsNotifications[] = $newsNotification;
            $newsNotification->setNews($this);
        }

        return $this;
    }

    public function removeNewsNotification(NewsNotification $newsNotification): self
    {
        if ($this->newsNotifications->contains($newsNotification)) {
            $this->newsNotifications->removeElement($newsNotification);
            // set the owning side to null (unless already changed)
            if ($newsNotification->getNews() === $this) {
                $newsNotification->setNews(null);
            }
        }

        return $this;
    }


}
