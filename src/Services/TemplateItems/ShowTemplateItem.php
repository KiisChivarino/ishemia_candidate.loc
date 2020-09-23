<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;

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
     */
    public function __construct()
    {
        parent::__construct();
        $this->addContentArray(self::DEFAULT_CONTENT);
        $this->setName(self::TEMPLATE_ITEM_SHOW_NAME);
    }
}