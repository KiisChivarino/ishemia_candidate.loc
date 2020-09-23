<?php

namespace App\Services\ControllerGetters;

use App\Services\FilterService\FilterService;

/**
 * Class FilterLabels
 *
 * @package App\Services\ControllerGetters
 */
class FilterLabels
{
    /** @var FilterService */
    private $filterService;

    /** @var array $filterLabels */
    private $filterLabelsArray;

    /**
     * FilterLabels constructor.
     *
     * @param FilterService $filterService
     */
    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    public function setFilterLabelsArray(array $filterLabelsArray): self
    {
        $this->filterLabelsArray = $filterLabelsArray;
        return $this;
    }

    /**
     * @return FilterService
     */
    public function getFilterService(): FilterService
    {
        return $this->filterService;
    }

    /**
     * @return array
     */
    public function getFilterLabelsArray(): array
    {
        return $this->filterLabelsArray;
    }
}