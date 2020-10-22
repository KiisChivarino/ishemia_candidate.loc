<?php

namespace App\Utils;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\PostPersist;
use Doctrine\ORM\Mapping\PostUpdate;
use Doctrine\ORM\Mapping\PreRemove;
use Doctrine\ORM\Mapping\PostRemove;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Spatie\ImageOptimizer\OptimizerChainFactory;

trait PatientFileTrait
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploadedDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $extension;

    /** @var UploadedFile $file */
    private $file;

    /** @var string $tempFilename */
    private $tempFilename;

    /**
     * @return UploadedFile
     */
    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile|null $file
     */
    public function setFile(UploadedFile $file = null): void
    {
        $this->file = $file;
        // Replacing a file ? Check if we already have a file for this entity
        if (null !== $this->extension) {
            // Save file extension so we can remove it later
            $this->tempFilename = $this->extension;
            // Reset values
            $this->extension = null;
            $this->fileName = null;
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        // If no file is set, do nothing
        if (null === $this->file) {
            return;
        }
        // The file name is the entity's ID
        // We also need to store the file extension
        $this->extension = $this->file->guessExtension();
        // And we keep the original name
        $this->fileName = $this->file->getClientOriginalName();
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // If no file is set, do nothing
        if (null === $this->file) {
            return;
        }
        // A file is present, remove it
        if (null !== $this->tempFilename) {
            $oldFile = $this->getUploadDir().'/'.$this->id.'.'.$this->tempFilename;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        $optimizerChain = OptimizerChainFactory::create();

        // Move the file to the upload folder
        $this->file->move(
            $this->getUploadDir(),
            $this->id.'.'.$this->extension
        );
        $optimizerChain
            ->optimize($this->getUploadDir().$this->id.'.'.$this->extension);
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUploadedDate(): ?DateTimeInterface
    {
        return $this->uploadedDate;
    }

    /**
     * @param DateTimeInterface $uploadedDate
     *
     * @return $this
     */
    public function setUploadedDate(DateTimeInterface $uploadedDate): self
    {
        $this->uploadedDate = $uploadedDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     *
     * @return $this
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload(): void
    {
        // Save the name of the file we would want to remove
        $this->tempFilename = $this->getUploadDir().'/'.$this->id.'.'.$this->extension;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload(): void
    {
        // PostRemove => We no longer have the entity's ID => Use the name we saved
        if (file_exists($this->tempFilename)) {
            // Remove file
            unlink($this->tempFilename);
        }
    }

    /**
     * @return string
     */
    public function getUploadDir(): string
    {
        // Upload directory
        return $this->getUploadRootDir().'/patient_files/';
        // This means /web/uploads/documents/
    }

    /**
     * @return string|string[]
     */
    protected function getUploadRootDir(): string
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        // Image location (PHP)
        return str_replace('src/Utils', 'data', __DIR__);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->id.'.'.$this->extension;
    }
}
