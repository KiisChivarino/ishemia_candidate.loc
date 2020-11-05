<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;
use App\Services\Template\TemplateService;

/**
 * Class FilterTemplateItem
 *
 * @package App\Services\TemplateItems
 */
class FilterTemplateItem extends TemplateItem
{
    /** @var string Name of filter template item */
    public const TEMPLATE_ITEM_FILTER_NAME = 'filter';

    /**
     * FilterTemplateItem constructor.
     * @param TemplateService $templateService
     */
    public function __construct(TemplateService $templateService)
    {
        parent::__construct($templateService);
        $this->setName(self::TEMPLATE_ITEM_FILTER_NAME);
        $this->setIsEnabled(false);
    }
}