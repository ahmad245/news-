<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CategoryController extends AbstractController
{
    private $em;
    private $repo;
  public function __construct(EntityManagerInterface $em,CategoryRepository $repo)
  {
    $this->em = $em;
    $this->repo = $repo;
  }
    /**
     * @Route("/admin/category", name="category")
     * 
     */
    public function index()
    {
      return $this->redirectToRoute('category_add');
    }

 /**
   * @Route("/admin/category/add", name="category_add")
   * @Security("is_granted('ROLE_ADMIN')")
   * @param Request $req
   * @return void
   */
    public function add(Request $req){
       $category=new Category();
       $form=$this->createForm(CategoryType::class,$category);
       $form->handleRequest($req);
       if ($form->isSubmitted() && $form->isValid()) {
        $this->em->persist($category);

       $this->em->flush();
 
      return $this->redirectToRoute('category_add');
       }

     
     
     return $this->render('category/create.html.twig', [
       'form' => $form->createView(),
       'categories'=>$this->repo->findAll()
     ]);
   }

   /**
   * @Route("/admin/category/edit/{id}",name="category_edit")
   *  @Security("is_granted('ROLE_ADMIN')")
   * @param Category $category
   * @param Request $req
   * @return void
   */
  public function edit(Category $category, Request $req)
  {

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
          $this->em->flush();
          return $this->redirectToRoute('category_add');
        }

      //  $this->em->flush();

      

        return $this->render('category/create.html.twig', [
        'form' => $form->createView(),
        'categories'=>$this->repo->findAll()
    ]);
}
/**
   * @Route("/admin/category/delete/{id}",name="category_delete")
   *  @Security("is_granted('ROLE_ADMIN')")
   
   * @param Category $category
   * @return void
   */
  public function delete(Category $category)
  {
    
    
     $this->em->remove($category);
    

     $this->em->flush();
    $this->addFlash('success', 'the category was deleted');
    return $this->redirectToRoute('category_add');
  }

}