<?php

namespace App\Services\EntityActions\Builder;

use App\Services\EntityActions\EntityActionsInterface;
use Closure;

/**
 * Class EntityActionsBuilder
 * for working with multiple objects of EntityActionsService
 * @package App\Services\EntityActions\Builder
 */
abstract class EntityActionsBuilder
{
    /**
     * @var EntityActionsInterface $entityActionsService
     */
    private $entityActionsService;

    /**
     * @var array $afterOptions
     * options for method "before" of entity actions service
     */
    private $beforeOptions;

    /**
     * options for method "after" of entity actions service
     * @var Closure|null
     */
    private $afterOptions;

    /**
     * EntityActionsBuilder constructor.
     * @param array $beforeOptions
     * @param Closure|null $addAfterOptionsFunction
     */
    public function __construct(
        array $beforeOptions = [],
        ?Closure $addAfterOptionsFunction = null
    )
    {
        $this->afterOptions = $addAfterOptionsFunction;
        $this->beforeOptions = $beforeOptions;
    }

    /**
     * @return EntityActionsInterface
     */
    public function getEntityActionsService(): EntityActionsInterface
    {
        return $this->entityActionsService;
    }

    /**
     * @param EntityActionsInterface $entityActionsService
     */
    public function setEntityActionsService(EntityActionsInterface $entityActionsService): void
    {
        $this->entityActionsService = $entityActionsService;
    }

    /**
     * @return array
     */
    public function getBeforeOptions(): array
    {
        return $this->beforeOptions;
    }

    /**
     * @param array $beforeOptions
     */
    public function setBeforeOptions(array $beforeOptions): void
    {
        $this->beforeOptions = $beforeOptions;
    }

    /**
     * @return Closure
     */
    public function getAfterOptions(): ?Closure
    {
        return $this->afterOptions;
    }

    /**
     * @param array $afterOptions
     */
    public function setAfterOptions(array $afterOptions): void
    {
        $this->afterOptions = $afterOptions;
    }
}