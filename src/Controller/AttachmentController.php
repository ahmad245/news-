<?php

namespace App\Controller;

use App\Entity\News;
use App\Services\AttachmentManager;
use App\Repository\AttachmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Flex\Response;

class AttachmentController extends AbstractController
{
    private $attachmentManager;

    public function __construct(AttachmentManager $attachmentManager)
    {
        $this->attachmentManager = $attachmentManager;
    }

    /**
     * @Route("/attachment", name="upload_attachment_firstTime")
     * @param Request $request
     * @param News $news
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function uploadsImageFirstTime(Request $request)
    {
        $file = $request->files->get('file');
      //  dd($file);die;
     
       

       $filenameAndPath = $this->attachmentManager->uploadAttachmentFirstTime($file);

        return $this->json([
            'location' => $filenameAndPath['path']
        ]);
    }

    /**
     * @Route("/attachment/{id}", name="upload_attachment")
     * @param Request $request
     * @param News $news
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function uploadsImage(Request $request,News $news)
    {
        $file = $request->files->get('file');
       

       $filenameAndPath = $this->attachmentManager->uploadAttachment($file, $news);

        return $this->json([
            'location' => $filenameAndPath['path']
        ]);
    }

    /**
     * @Route("/removeAll")
     * @param AttachmentRepository $attachmentRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function removeAll(AttachmentRepository $attachmentRepository, EntityManagerInterface $entityManager)
    {
        foreach ($attachmentRepository->findAll() as $attachment)
        {
            $entityManager->remove($attachment);
        }
        $entityManager->flush();

        return new Response("awdwd");
    }
}