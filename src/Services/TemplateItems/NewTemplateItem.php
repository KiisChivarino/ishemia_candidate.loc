<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;
use App\Services\Template\TemplateService;

/**
 * Class NewTemplateItem
 *
 * @package App\Services\TemplateItems
 */
class NewTemplateItem extends TemplateItem
{
    /** @var string Name of New template item */
    public const TEMPLATE_ITEM_NEW_NAME = 'new';

    /**
     * NewTemplateItem constructor.
     * @param TemplateService $templateService
     */
    public function __construct(TemplateService $templateService)
    {
        parent::__construct($templateService);
        $this->setName(self::TEMPLATE_ITEM_NEW_NAME);
    }
}