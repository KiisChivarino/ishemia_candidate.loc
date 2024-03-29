<?php

namespace App\Entity;

use App\Repository\DischargeEpicrisisFileRepository;
use App\Utils\PatientFileTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DischargeEpicrisisFileRepository::class)
 * @ORM\Table(options={"comment":"Файлы выписных эпикризов"});
 * @ORM\HasLifecycleCallbacks
 */
class DischargeEpicrisisFile
{
    /** @var string Upload files dir */
    public const UPLOAD_DIR = 'patientDischargeEpicrisisFiles';

    use PatientFileTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ файла выписного эпикриза"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(
     *     targetEntity=PatientDischargeEpicrisis::class,
     *     inversedBy="dischargeEpicrisisFiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $patientDischargeEpicrisis;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return PatientDischargeEpicrisis|null
     */
    public function getPatientDischargeEpicrisis(): ?PatientDischargeEpicrisis
    {
        return $this->patientDischargeEpicrisis;
    }

    /**
     * @param PatientDischargeEpicrisis|null $patientDischargeEpicrisis
     *
     * @return $this
     */
    public function setPatientDischargeEpicrisis(?PatientDischargeEpicrisis $patientDischargeEpicrisis): self
    {
        $this->patientDischargeEpicrisis = $patientDischargeEpicrisis;
        return $this;
    }
}
