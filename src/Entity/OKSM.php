<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ОКСМ
 * @ORM\Entity(repositoryClass="App\Repository\OKSMRepository")
 * @ORM\Table(options={"comment":"ОКСМ"});
 */
class OKSM
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ ОКСМ"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2, options={"comment"="Двузначный код страны"})
     */
    private $A2;

    /**
     * @ORM\Column(type="string", length=3, options={"comment"="Трехзначный код страны"})
     */
    private $A3;

    /**
     * @ORM\Column(type="integer", options={"comment"="Числовой код страны"})
     */
    private $N3;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название страны"})
     */
    private $caption;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getA2(): ?string
    {
        return $this->A2;
    }

    public function setA2(string $A2): self
    {
        $this->A2 = $A2;

        return $this;
    }

    public function getA3(): ?string
    {
        return $this->A3;
    }

    public function setA3(string $A3): self
    {
        $this->A3 = $A3;

        return $this;
    }

    public function getN3(): ?int
    {
        return $this->N3;
    }

    public function setN3(int $N3): self
    {
        $this->N3 = $N3;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }
}
