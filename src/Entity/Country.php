<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Страна
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 * @ORM\Table(options={"comment":"Страна"});
 */
class Country
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ страны"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30, options={"comment"="Название страны"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=4, options={"comment"="Код страны в формате ISO"})
     */
    private $shortcode;

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
    public function getShortcode(): ?string
    {
        return $this->shortcode;
    }

    /**
     * @param string $shortcode
     *
     * @return $this
     */
    public function setShortcode(string $shortcode): self
    {
        $this->shortcode = $shortcode;
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
