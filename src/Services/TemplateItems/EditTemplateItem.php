<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;
use App\Services\Template\TemplateService;

class EditTemplateItem extends TemplateItem
{
    /** @var string Name of Edit template item */
    public const TEMPLATE_ITEM_EDIT_NAME = 'edit';

    /**
     * EditTemplateItem constructor.
     * @param TemplateService $templateService
     */
    public function __construct(TemplateService $templateService)
    {
        parent::__construct($templateService);
        $this->setName(self::TEMPLATE_ITEM_EDIT_NAME);
        $this->setItemRouteName(self::TEMPLATE_ITEM_EDIT_NAME);
    }
}