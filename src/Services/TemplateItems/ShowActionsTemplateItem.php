<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;
use App\Services\Template\TemplateService;

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
     * @param TemplateService $templateService
     */
    public function __construct(TemplateService $templateService)
    {
        parent::__construct($templateService);
        $this->setName(self::TEMPLATE_NAME);
    }
}