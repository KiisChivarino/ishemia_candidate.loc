<?php

namespace App\Entity;

use App\Repository\NotificationTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class NotificationType
 * @ORM\Entity(repositoryClass=NotificationTypeRepository::class)
 * @ORM\Table(options={"comment":"Тип уведомления"});
 *
 * @package App\Entity
 */
class NotificationType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ типа уведомления"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Наименование типа уведомления"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Шаблон типа уведомления"})
     */
    private $template;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * @param string|null $template
     *
     * @return $this
     */
    public function setTemplate(?string $template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }
}
