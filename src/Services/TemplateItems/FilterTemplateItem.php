<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;

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
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName(self::TEMPLATE_ITEM_FILTER_NAME);
        $this->setIsEnabled(false);
    }
}