<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AdminSecurity extends Voter{

     const ADMIN="ADMIN";
  
     private $security;

     public function __construct(Security $security)
     {
         $this->security = $security;
     }
     /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
     protected function supports(string $attribute, $subject){
         if (!in_array($attribute,[self::ADMIN]))  return  false;
         if(!$subject instanceof User) return false;
         return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param mixed $subject
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token){
        $authentecated=$token->getUser();
        if(!$authentecated instanceof User) return false;
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
       
        
         return false;
     }
}