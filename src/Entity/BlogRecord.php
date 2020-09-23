<?php

namespace App\Entity;

use App\Repository\BlogRecordRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BlogRecordRepository::class)
 */
class BlogRecord
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dateBegin;

    /**
     * @ORM\Column(type="date")
     */
    private $dateEnd;

    /**
     * @ORM\OneToMany(targetEntity=BlogItem::class, mappedBy="blogRecord")
     */
    private $blogItems;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $version;

    /**
     * BlogRecord constructor.
     */
    public function __construct()
    {
        $this->blogItems = new ArrayCollection();
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
    public function getDateBegin(): ?DateTimeInterface
    {
        return $this->dateBegin;
    }

    /**
     * @param DateTimeInterface $dateBegin
     *
     * @return $this
     */
    public function setDateBegin(DateTimeInterface $dateBegin): self
    {
        $this->dateBegin = $dateBegin;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateEnd(): ?DateTimeInterface
    {
        return $this->dateEnd;
    }

    /**
     * @param DateTimeInterface $dateEnd
     *
     * @return $this
     */
    public function setDateEnd(DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * @return Collection|BlogItem[]
     */
    public function getBlogItems(): Collection
    {
        return $this->blogItems;
    }

    /**
     * @param BlogItem $blogItem
     *
     * @return $this
     */
    public function addBlogItem(BlogItem $blogItem): self
    {
        if (!$this->blogItems->contains($blogItem)) {
            $this->blogItems[] = $blogItem;
            $blogItem->setBlogRecord($this);
        }
        return $this;
    }

    /**
     * @param BlogItem $blogItem
     *
     * @return $this
     */
    public function removeBlogItem(BlogItem $blogItem): self
    {
        if ($this->blogItems->contains($blogItem)) {
            $this->blogItems->removeElement($blogItem);
            // set the owning side to null (unless already changed)
            if ($blogItem->getBlogRecord() === $this) {
                $blogItem->setBlogRecord(null);
            }
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string|null $version
     *
     * @return $this
     */
    public function setVersion(?string $version): self
    {
        $this->version = $version;
        return $this;
    }
}
