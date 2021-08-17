<?php

namespace App\Services\TemplateItems\PatientOfficeItems;

use App\Services\Template\TemplateItem;
use App\Services\Template\TemplateService;

/**
 * Element of template - History list
 */
class HistoryListTemplateItem extends TemplateItem
{
    /** @var string Name of Edit template item */
    public const TEMPLATE_ITEM_HISTORY_LIST_NAME = 'historyList';

    /** @var string[] Common content of history list template item */
    public const DEFAULT_CONTENT = [
        'toHistoryLink' => 'История',
    ];

    /**
     * EditTemplateItem constructor.
     * @param TemplateService $templateService
     */
    public function __construct(TemplateService $templateService)
    {
        parent::__construct($templateService);
        $this->addContentArray(self::DEFAULT_CONTENT);
        $this->setName(self::TEMPLATE_ITEM_HISTORY_LIST_NAME);
    }
}
