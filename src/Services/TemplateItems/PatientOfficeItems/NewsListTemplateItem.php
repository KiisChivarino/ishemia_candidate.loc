<?php

namespace App\Services\TemplateItems\PatientOfficeItems;

use App\Services\Template\TemplateItem;
use App\Services\Template\TemplateService;

/**
 * News list template item
 */
class NewsListTemplateItem extends TemplateItem
{
    /** @var string Name of Edit template item */
    public const TEMPLATE_ITEM_NEWS_LIST_NAME = 'newsList';

    /** @var string[] Common content of news list template item */
    public const DEFAULT_CONTENT = [
        'toNewsLink' => 'Новые',
    ];

    /**
     * EditTemplateItem constructor.
     * @param TemplateService $templateService
     */
    public function __construct(TemplateService $templateService)
    {
        parent::__construct($templateService);
        $this->addContentArray(self::DEFAULT_CONTENT);
        $this->setName(self::TEMPLATE_ITEM_NEWS_LIST_NAME);
    }
}
