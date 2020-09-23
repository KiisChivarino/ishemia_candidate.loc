<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;

/**
 * Class TableActionsTemplateItem
 *
 * @package App\Services\TemplateItems
 */
class TableActionsTemplateItem extends TemplateItem
{
    /** @var string Name of TableActions template item */
    public const TEMPLATE_ITEM_SHOW_ACTIONS_NAME = 'tableActions';

    /** @var string[] Контент по умолчанию */
    protected const DEFAULT_CONTENT = [
        'deleteConfirm' => 'Вы уверены, что хотите удалить запись?',
    ];

    /**
     * TableActionsTemplateItem constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->addContentArray(self::DEFAULT_CONTENT);
        $this->setName(self::TEMPLATE_ITEM_SHOW_ACTIONS_NAME);
    }
}