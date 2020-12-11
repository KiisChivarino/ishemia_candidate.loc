<?php

namespace App\Entity;

use App\Repository\SMSNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * SMS Notification
 * @ORM\Entity(repositoryClass=SMSNotificationRepository::class)
 */
class SMSNotification
{
    /** @var int Перввая попытка */
    const FIRST_ATTEMPT = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AuthUser::class, inversedBy="sMSNotifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $attempt;

    /**
     * SMSNotification constructor.
     */
    public function __construct()
    {
        $this->attempt = self::FIRST_ATTEMPT;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return AuthUser|null
     */
    public function getUser(): ?AuthUser
    {
        return $this->user;
    }

    /**
     * @param AuthUser|null $user
     * @return $this
     */
    public function setUser(?AuthUser $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeInterface $created_at
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return $this
     */
    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttempt()
    {
        return $this->attempt;
    }

    /**
     * @param mixed $attempt
     */
    public function setAttempt($attempt): void
    {
        $this->attempt = $attempt;
    }
}
