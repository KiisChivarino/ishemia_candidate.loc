<?php

namespace App\Entity;

use App\Repository\ReceivedSMSRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReceivedSMSRepository::class)
 */
class ReceivedSMS
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ полученной sms"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="receivedSMS")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $patient;

    /**
     * @ORM\Column(type="datetime", options={"comment"="ата и время отправки sms"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment"="Текст полученной sms"})
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="ID sms сообщения на стороне провайдера"})
     */
    private $externalId;

    /**
     * @return mixed
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param mixed $externalId
     */
    public function setExternalId($externalId): void
    {
        $this->externalId = $externalId;
    }

    /**
     * @ORM\Column(type="boolean")
     */
    private $isProcessed;

    /**
     * ReceivedSMS constructor.
     */
    public function __construct()
    {
        $this->isProcessed = false;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param DateTimeInterface $created_at
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return $this
     */
    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsProcessed(): ?bool
    {
        return $this->isProcessed;
    }

    /**
     * @param bool $isProcessed
     * @return $this
     */
    public function setIsProcessed(bool $isProcessed): self
    {
        $this->isProcessed = $isProcessed;

        return $this;
    }

    /**
     * @return Patient|null
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * @param Patient|null $patient
     * @return $this
     */
    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

}
