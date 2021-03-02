<?php

namespace App\Services\EntityActions\Builder;

use App\Services\EntityActions\Creator\AbstractCreatorService;

/**
 * Class CreatorEntityActionsBuilder
 * contains CreatorEntityActions object with options
 * @package App\Services\EntityActions\Builder
 */
class CreatorEntityActionsBuilder extends EntityActionsBuilder
{
    /**
     * CreatorEntityActionsBuilder constructor.
     * @param AbstractCreatorService $creatorService
     * @param array $beforeOptions
     * @param array $afterOptions
     */
    public function __construct(
        AbstractCreatorService $creatorService,
        array $beforeOptions = [],
        array $afterOptions = []
    )
    {
        parent::__construct($beforeOptions, $afterOptions);
        $this->setEntityActionsService($creatorService);
    }
}