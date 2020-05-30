<?php

namespace App\Services;

use App\Entity\Attachment;
use App\Entity\News;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;



class AttachmentManager
{

    private $container;

    private $entityManager;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }
    public function uploadAttachmentFirstTime(UploadedFile $file)
    {
        $filename = md5(uniqid()) . '.' . $file->guessClientExtension();

        $file->move(
            $this->getUploadsDirectory(),
            $filename
        );
        return [
            'path' => '/uploads/' . $filename,
            'filename' => $filename
        ];
    }

    public function uploadAttachment(UploadedFile $file, News $news)
    {
        $filename = md5(uniqid()) . '.' . $file->guessClientExtension();

        $file->move(
            $this->getUploadsDirectory(),
            $filename
        );

        $attachment = new Attachment();

        $attachment->setFilename($filename);
        $attachment->setPath('/uploads/' . $filename);

        $attachment->setNews($news);
        $news->addAttachment($attachment);

        $this->entityManager->persist($attachment);
        $this->entityManager->flush();

        return [
            'path' => '/uploads/' . $filename,
            'filename' => $filename
        ];
    }

    public function removeAttachment(?string $filename)
    {
        //var_dump( $this->getUploadsDirectory() .  $filename);die;
        if (!empty($filename)) {
            $filesystem = new Filesystem();

            $filesystem->remove(
                $this->getUploadsDirectory() .  $filename
            );
        }
    }

    public function getUploadsDirectory()
    {
        return $this->container->getParameter('uploads');
    }
}
