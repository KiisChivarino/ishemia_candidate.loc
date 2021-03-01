<?php

namespace App\Services\EntityActions\Builder;

use App\Services\EntityActions\EntityActionsInterface;

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
     * @var array $afterOptions
     * options for method "after" of entity actions service
     */
    private $afterOptions;

    /**
     * EntityActionsBuilder constructor.
     * @param array $beforeOptions
     * @param array $afterOptions
     */
    public function __construct(
        array $beforeOptions = [],
        array $afterOptions = []
    )
    {
        $this->afterOptions = $afterOptions;
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
     * @return array
     */
    public function getAfterOptions(): array
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