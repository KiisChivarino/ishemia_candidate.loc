<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;

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
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName(self::TEMPLATE_ITEM_NEW_NAME);
    }
}