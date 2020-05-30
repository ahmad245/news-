<?php

namespace App\Entity;
use App\Entity\Role;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTimeInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 */
class User implements UserInterface, \Serializable
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *  min=2,
     *  max=30,
     * minMessage="Your First Name  must be at least {{ limit }} characters long",
     * maxMessage="Your First Name  cannot be longer than {{ limit }} characters"
     * )
     *   @Groups("user")
     *
     */
    private $firstName;

    /**
     * @ORM\Column(type="string",length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *  min=2,
     *  max=50,
     * minMessage="Your Last Name  must be at least {{ limit }} characters long",
     * maxMessage="Your Last Name  cannot be longer than {{ limit }} characters"
     * )
     * @Groups("user")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string",length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(
     *  min=3,
     *  max=255,
     * minMessage="Your Last Name  must be at least {{ limit }} characters long",
     * maxMessage="Your Last Name  cannot be longer than {{ limit }} characters"
     * )
     * @Groups("user")
     */
    private $email;


    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     *
     */
    private $createAt;

  

    /**
     * @ORM\Column(type="string",length=255)
     * @Assert\NotBlank()
      * @Assert\Regex(
     *   pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *   message="Password must be seven charachters and contain at least one digit,one uppercase and one lowercase"
     * )
     */
    private $password;

     /**
     *  @Assert\EqualTo(
     *   propertyPath="password",
     *     message="Passwords does not match"
     * )
     */

    private $confirmPassword;

   


 
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\News" , mappedBy="user")
     */
    private $news;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $userRoles;

    private $roles;

     /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;



    /**
     * @ORM\OneToMany(targetEntity=NewsNotification::class, mappedBy="createdBy")
     */
    private $newsNotifications;

    /**
     * @ORM\OneToMany(targetEntity=NotificationUser::class, mappedBy="user")
     */
    private $notificationUsers;

  

 



    public function __construct()
    {
        $this->news=new ArrayCollection();
        $this->userRoles=new ArrayCollection();
        $this->enabled=true;
    
        $this->newsNotifications = new ArrayCollection();
        $this->notificationUsers = new ArrayCollection();
  
  
   
    }

    /**
     * 
     */

    public function getId(){
        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Undocumented function
     *
     * @param string $firstName
     * @return self
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }
  
    /**
     * 
     */

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFullName():?string
    {
        return $this->firstName.' '.$this->lastName ;
    }

    /**
     * Undocumented function
     *
     * @param string $lastName
     * @return self
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Undocumented function
     *
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }




    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Undocumented function
     *
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    /**
     * Undocumented function
     *
     * @param string $confirmPassword
     * @return self
     */
    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    

    ///////////////////////////////////////////////////
    
    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(){  return null;}

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(){
        return $this->email;
    }

    public function setUsername(string $email):self
    {
         $this->username=$email;
         return $this;
    }
    public function fullName(){
        return " {$this->firstName} {$this->lastName} ";
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(){}

    public function getRoles(){
        $roles=  $this->userRoles->map(function($role){
             return $role->getName();
         })->toArray();
 
          $roles[]='ROLE_USER';
         
         return $roles;
     }

    ///////////////////////////////////////////
    /**
     * @return Collection|news[]
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    /**
     * Undocumented function
     *
     * @param News $news
     * @return self
     */
    public function addNews(News $news):self
    {
        if(!$this->news->contains($news)){
            $this->news[]=$news;
            $news->setUser($this);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param News $news
     * @return self
     */
    public function removeNews(News $news):self
    {
        if($this->news->contains($news))
        {
            $this->news->removeElement($news);

            if($news->getUser()==$this)
            {
                $news->setUser(null);
            }
        }
        return $this;
    }
    /////////////////////////////////////////
      /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    /**
     * Undocumented function
     *
     * @param Role $userRole
     * @return self
     */
    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Role $userRole
     * @return self
     */
    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUser($this);
        }

        return $this;
    }





    
   
 

    ///////////////////////////////////////////

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function initDate(){
        $this->createAt=new  \DateTime();
    }

    /**
     * Undocumented function
     *
     * @return \DateTimeInterface|null
     */
    public function getCreateAt(): ?\DateTimeInterface 
    {
        return $this->createAt;
    }

   /**
    * Undocumented function
    *
    * @param \DateTimeInterface $createAt
    * @return self
    */
    public function setCreateAt(\DateTimeInterface $createAt):self
    {
        $this->createAt = $createAt;

        return $this;
    }

   
   
 


   public function serialize()
   {
       return serialize(
           [
               $this->id,
               $this->firstName,
               $this->lastName,
               $this->email,
               $this->password,
               $this->enabled
          
            
           ]
       );
   }

   public function unserialize($serialized)
   {
       list(
           $this->id,  
           $this->firstName,
           $this->lastName,
           $this->email, 
           $this->password,
           $this->enabled
      
           ) = unserialize($serialized);
   }



    /**
     * Get the value of enabled
     */ 
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set the value of enabled
     *
     * @return  self
     */ 
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function __toString()
    {
        return $this->getFullName();
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
            $newsNotification->setCreatedBy($this);
        }

        return $this;
    }

    public function removeNewsNotification(NewsNotification $newsNotification): self
    {
        if ($this->newsNotifications->contains($newsNotification)) {
            $this->newsNotifications->removeElement($newsNotification);
            // set the owning side to null (unless already changed)
            if ($newsNotification->getCreatedBy() === $this) {
                $newsNotification->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|NotificationUser[]
     */
    public function getNotificationUsers(): Collection
    {
        return $this->notificationUsers;
    }

    public function addNotificationUser(NotificationUser $notificationUser): self
    {
        if (!$this->notificationUsers->contains($notificationUser)) {
            $this->notificationUsers[] = $notificationUser;
            $notificationUser->setUser($this);
        }

        return $this;
    }

    public function removeNotificationUser(NotificationUser $notificationUser): self
    {
        if ($this->notificationUsers->contains($notificationUser)) {
            $this->notificationUsers->removeElement($notificationUser);
            // set the owning side to null (unless already changed)
            if ($notificationUser->getUser() === $this) {
                $notificationUser->setUser(null);
            }
        }

        return $this;
    }


}