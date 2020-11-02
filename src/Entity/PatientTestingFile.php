<?php

namespace App\Entity;

use App\Repository\PatientTestingFileRepository;
use App\Utils\PatientFileTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientTestingFileRepository::class)
 * @ORM\Table(options={"comment":"Файлы обследований"});
 * @ORM\HasLifecycleCallbacks
 */
class PatientTestingFile
{
    use PatientFileTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ файла обследования"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PatientTesting::class, inversedBy="patientTestingFiles",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $patientTesting;

    private $fileName;

    private $uploadedDate;

    private $extension;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return PatientTesting|null
     */
    public function getPatientTesting(): ?PatientTesting
    {
        return $this->patientTesting;
    }

    /**
     * @param PatientTesting|null $patientTesting
     *
     * @return $this
     */
    public function setPatientTesting(?PatientTesting $patientTesting): self
    {
        $this->patientTesting = $patientTesting;
        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getUploadedDate(): ?\DateTimeInterface
    {
        return $this->uploadedDate;
    }

    public function setUploadedDate(\DateTimeInterface $uploadedDate): self
    {
        $this->uploadedDate = $uploadedDate;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }
}
