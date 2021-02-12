<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;
use App\Services\Template\TemplateService;

/**
 * Class ListTemplateItem
 *
 * @package App\Services\TemplateItems
 */
class ListTemplateItem extends TemplateItem
{
    /** @var string Name of List template item */
    public const TEMPLATE_ITEM_LIST_NAME = 'list';

    /** @var string[] Common content of list template item */
    public const DEFAULT_CONTENT = [
        'title' => 'Список записей',
        'h1' => 'Список записей',
        'loadTableData' => 'Загрузка данных, пожалуйста, подождите...',
        'toNew' => 'Новая запись',
        'operations' => 'Операции',
        'serialNumber' => '№',
    ];

    /**
     * ListTemplateItem constructor.
     * @param TemplateService $templateService
     */
    public function __construct(TemplateService $templateService)
    {
        parent::__construct($templateService);
        $this->addContentArray(self::DEFAULT_CONTENT);
        $this->setName(self::TEMPLATE_ITEM_LIST_NAME);
    }
}