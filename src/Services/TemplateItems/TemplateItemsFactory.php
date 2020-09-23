<?php

namespace App\Services\TemplateItems;

/**
 * Class TemplateItemsFactory
 *
 * @package App\Services\TemplateItems
 */
class TemplateItemsFactory
{
    /**
     * Returns template items for List template
     *
     * @return array
     */
    public function getListTemplateItems()
    {
        return [
            new ListTemplateItem(),
            new FilterTemplateItem(),
            new NewTemplateItem(),
            new TableActionsTemplateItem(),
            new ShowTemplateItem(),
            new DeleteTemplateItem(),
            new EditTemplateItem(),
        ];
    }

    /**
     * Returns template items for Show template
     *
     * @return array
     */
    public function getShowTemplateItems()
    {
        return [
            new ShowTemplateItem(),
            new ShowActionsTemplateItem(),
            new ListTemplateItem(),
            new EditTemplateItem(),
            new DeleteTemplateItem()
        ];
    }

    /**
     * Returns template items for New template
     *
     * @return array
     */
    public function getNewTemplateItems()
    {
        return [
            new NewTemplateItem(),
            new FilterTemplateItem(),
            new FormTemplateItem(),
            new ListTemplateItem(),
        ];
    }

    /**
     * Returns template items for Edit template
     *
     * @return array
     */
    public function getEditTemplateItems()
    {
        return [
            new EditTemplateItem(),
            new FilterTemplateItem(),
            new FormTemplateItem(),
            new ListTemplateItem(),
            new DeleteTemplateItem(),
        ];
    }
}