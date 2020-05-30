<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminAccountType;
use App\Form\AdminAccountEditType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Services\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends AbstractController
{

    private $em;
    private $repoUser;
    public function __construct(EntityManagerInterface $em, UserRepository $repoUser)
    {
        $this->em = $em;
        $this->repoUser = $repoUser;
    }
    /**
     * @Route("/admin/user/{page}", name="admin_user", requirements={"page":"\d+"})
     *  @Security("is_granted('ROLE_ADMIN')")
     *  
     */
    public function index($page=1,PaginationService $pagination)
    {
        $pagination->setEntityClass(User::class)->setLimit(5)->setPage($page);
      //  $users = $this->repoUser->findAll();
        return $this->render('admin/admin_user/index.html.twig', [
            'controller_name' => 'AdminUserController',
           // 'users' => $users,
            'pagination'=>$pagination
        ]);
    }

    /**
     * @Route("/admin/user/add",name="admin_user_add")
     *  @Security("is_granted('ROLE_ADMIN')")
     *
     */

    public function create(Request $req, UserPasswordEncoderInterface $encode, RoleRepository $repoRole)
    {
        $user = new User();
        $form = $this->createForm(AdminAccountType::class, $user);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {



            $user->setPassword($encode->encodePassword($user, $user->getPassword()));
            foreach ($user->getUserRoles() as $role) {
                $user->addUserRole($role);
                $role->addUser($user);
            }

            $this->em->persist($user);
            $this->em->flush();



            return $this->redirectToRoute('admin_user');
        }
        return $this->render('admin/admin_user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/edit/{id}",name="admin_user_edit")
     *  @Security("is_granted('ROLE_ADMIN')")
     *  
     */

    public function edit(User $user, Request $req, UserPasswordEncoderInterface $encode, RoleRepository $repoRole, UserRepository $repoUser)
    {

        $prevRoles = $user->getUserRoles();

        $form = $this->createForm(AdminAccountEditType::class, $user);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            // $user->setPassword($encode->encodePassword($user, $user->getPassword()));
            $nextRoles = $user->getUserRoles();

            foreach ($user->getUserRoles() as $role) {
                $role->addUser($user);
            }

            foreach ($repoRole->findAll()  as $prev) {
                if (!in_array($prev, $nextRoles->toArray())) {
                    $prev->removeUser($user);
                }
            }
            $this->em->flush();
            return $this->redirectToRoute('admin_user');
        }
        return $this->render('admin/admin_user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
