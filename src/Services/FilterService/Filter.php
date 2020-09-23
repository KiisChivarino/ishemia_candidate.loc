<?php


namespace App\Services\FilterService;

/**
 * Class Filter
 *
 * @package App\Services\FilterService
 */
class Filter
{
    /** @var string|null $name */
    private $name;
    /** @var string|null $entityClass */
    private $entityClass;
    /** @var string|null $value */
    private $value;

    /**
     * Filter constructor.
     *
     * @param string|null $name
     * @param string|null $entityClass
     * @param string|null $value
     */
    public function __construct(string $name, string $entityClass, string $value = null)
    {
        $this->name = $name;
        $this->entityClass = $entityClass;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass(string $entityClass): void
    {
        $this->entityClass = $entityClass;
    }
}