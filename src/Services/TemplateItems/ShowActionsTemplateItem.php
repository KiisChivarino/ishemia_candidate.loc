<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;

/**
 * Class ShowActionsTemplateItem
 *
 * @package App\Services\TemplateItems
 */
class ShowActionsTemplateItem extends TemplateItem
{
    /** @var string Name of ShowActions template item */
    public const TEMPLATE_NAME = 'showActions';

    /**
     * ShowActionsTemplateItem constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName(self::TEMPLATE_NAME);
    }
}