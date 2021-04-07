<?php

namespace App\Services\EntityActions\Builder;

use App\Services\EntityActions\Creator\AbstractCreatorService;
use Closure;

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
     * @param Closure|null $afterOptions
     */
    public function __construct(
        AbstractCreatorService $creatorService,
        array $beforeOptions = [],
        ?Closure $afterOptions = null
    )
    {
        parent::__construct($beforeOptions, $afterOptions);
        $this->setEntityActionsService($creatorService);
    }
}