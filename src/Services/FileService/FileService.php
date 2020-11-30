<?php

namespace App\Services\FileService;

use DateTime;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileService
 * @package App\Services\FileService
 */
class FileService
{
    /** @var string Directory with files of users */
    public const FILES_DIR = 'patient_files';

    /**
     * Prepare files, because preUpload and prePersist dont`t work...WHY???
     *
     * @param FormInterface $filesForm
     */
    public function prepareFiles(
        FormInterface $filesForm
    ): void
    {
        foreach ($filesForm->all() as $fileForm) {
            $fileEntity = $fileForm->getData();
            if ($fileEntity) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $fileForm->get('file')->getData();
                if ($uploadedFile) {
                    $this->setFileProperties($fileEntity, $uploadedFile);
                }
            }
        }
    }

    /**
     * Set property values for file entity
     *
     * @param object $fileEntity
     * @param UploadedFile $uploadedFile
     */
    public function setFileProperties(object $fileEntity, UploadedFile $uploadedFile): void
    {
        $fileEntity->setFile($uploadedFile);
        $fileEntity->setFileName($uploadedFile->getClientOriginalName());
        $fileEntity->setExtension(preg_replace('/.+\//', '', $uploadedFile->getMimeType()));
        $fileEntity->setUploadedDate(new DateTime());
    }
}