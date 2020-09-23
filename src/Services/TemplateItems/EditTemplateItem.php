<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;

class EditTemplateItem extends TemplateItem
{
    /** @var string Name of Edit template item */
    public const TEMPLATE_ITEM_EDIT_NAME = 'edit';

    /**
     * EditTemplateItem constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName(self::TEMPLATE_ITEM_EDIT_NAME);
    }
}