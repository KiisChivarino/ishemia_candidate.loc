<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;
use App\Services\Template\TemplateService;

/**
 * Class DeleteTemplateItem
 * class for Delete template item
 *
 * @package App\Services\TemplateItems
 */
class DeleteTemplateItem extends TemplateItem
{
    /** @var string Delete template item name */
    public const TEMPLATE_ITEM_DELETE_NAME = 'delete';

    /** @var string[] Контент по умолчанию */
    protected const DEFAULT_CONTENT = [
        'deleteConfirm' => 'Вы уверены, что хотите удалить запись?',
        'deleteButton' => 'Удалить',
    ];

    /**
     * DeleteTemplateItem constructor.
     * @param TemplateService $templateService
     */
    public function __construct(TemplateService $templateService)
    {
        parent::__construct($templateService);
        $this->addContentArray(self::DEFAULT_CONTENT);
        $this->setName(self::TEMPLATE_ITEM_DELETE_NAME);
    }
}