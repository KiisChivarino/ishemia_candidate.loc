<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateItem;

/**
 * Class FormTemplateItem
 *
 * @package App\Services\TemplateItems
 */
class FormTemplateItem extends TemplateItem
{
    /** @var string name of form template item */
    public const TEMPLATE_ITEM_FORM_NAME = 'form';

    /** @var string[] Common content of form template item */
    protected const DEFAULT_CONTENT = [
        'formButtonLabel' => 'Сохранить',
    ];

    /**
     * FormTemplateItem constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->addContentArray(self::DEFAULT_CONTENT);
        $this->setName(self::TEMPLATE_ITEM_FORM_NAME);
    }
}