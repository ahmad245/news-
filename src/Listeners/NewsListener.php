<?php
    
namespace App\Listeners;

use App\Entity\Attachment;
use App\Entity\News;
use App\Repository\AttachmentRepository;
use App\Services\AttachmentManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class NewsListener
{

    /**
     * @var AttachmentManager
     */
    private $attachmentManager;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AttachmentRepository
     */
    private $attachmentRepository;

    public function __construct(EntityManagerInterface $entityManager, AttachmentRepository $attachmentRepository, AttachmentManager $attachmentManager)
    {
        $this->attachmentManager = $attachmentManager;
        $this->entityManager = $entityManager;
        $this->attachmentRepository = $attachmentRepository;
    }

    public function preUpdate(News $news, PreUpdateEventArgs $args)
    {
       var_dump('prupdate');
        if ($args->hasChangedField('content')) {
//            $em = $args->getEntityManager();
            /** @var AttachmentRepository $attachmentRepository */
//            $attachmentRepository = $em->getRepository(Attachment::class);
            $regex = '~/uploads/[a-zA-Z0-9]+\.\w+~';
            $matches = [];

            if (preg_match_all($regex, $args->getNewValue('content'), $matches) > 0)
            {
                
                $filenames = array_map(function ($match) {
                    return basename($match);
                }, $matches[0]);

              

                $recordsToRemove = $this->attachmentRepository->findAttachmentsToRemove($filenames, $news->getId());
                
                /** @var Attachment $record */
                foreach ($recordsToRemove as $record)
                {
                    // remove the record from the db
                    $this->entityManager->remove($record);
                    // remove the file from the server
                    $this->attachmentManager->removeAttachment($record->getFilename());
                }

            } 
            else if ($news->getAttachments()->count() && $matches) {
                /** @var Attachment $record */
                foreach ($news->getAttachments() as $record)
                {
                    // remove the record from the db
                    $entity = $this->entityManager->merge($record);
                    $this->entityManager->remove($entity);
                    // remove the file from the server
                    $this->attachmentManager->removeAttachment($record->getFilename());
                }
            }
           
           
           
 
          
            $this->entityManager->flush();
        }
        // $this->entityManager->persist($news);
        // $this->entityManager->flush();
    }
}