<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;
use App\Services\Template\TemplateService;

/**
 * Class ShowTemplateItem
 *
 * @package App\Services\TemplateItems
 */
class ShowTemplateItem extends TemplateItem
{
    /** @var string Name of Show template item */
    public const TEMPLATE_ITEM_SHOW_NAME = 'show';

    /** @var string[] Контент по умолчанию */
    protected const DEFAULT_CONTENT = [
        'title' => 'Просмотр записи',
        'h1' => 'Просмотр записи',
    ];

    /**
     * ShowTemplateItem constructor.
     * @param TemplateService $templateService
     */
    public function __construct(TemplateService $templateService)
    {
        parent::__construct($templateService);
        $this->addContentArray(self::DEFAULT_CONTENT);
        $this->setName(self::TEMPLATE_ITEM_SHOW_NAME);
    }
}