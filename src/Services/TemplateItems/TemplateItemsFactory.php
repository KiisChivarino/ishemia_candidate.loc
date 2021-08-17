<?php

namespace App\Services\TemplateItems;

use App\Services\Template\TemplateService;
use App\Services\TemplateItems\PatientOfficeItems\HistoryListTemplateItem;
use App\Services\TemplateItems\PatientOfficeItems\NewsListTemplateItem;

/**
 * Class TemplateItemsFactory
 *
 * @package App\Services\TemplateItems
 */
class TemplateItemsFactory
{
    /** @var TemplateService $templateService*/
    private $templateService;

    /**
     * TemplateItemsFactory constructor.
     * @param TemplateService $templateService
     */
    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Returns template items for List template
     *
     * @return array
     */
    public function getListTemplateItems(): array
    {
        return [
            new ListTemplateItem($this->templateService),
            new FilterTemplateItem($this->templateService),
            new NewTemplateItem($this->templateService),
            new TableActionsTemplateItem($this->templateService),
            new ShowTemplateItem($this->templateService),
            new DeleteTemplateItem($this->templateService),
            new EditTemplateItem($this->templateService),
        ];
    }

    /**
     * Returns template items for Show template
     *
     * @return array
     */
    public function getShowTemplateItems(): array
    {
        return [
            new ShowTemplateItem($this->templateService),
            new ShowActionsTemplateItem($this->templateService),
            new ListTemplateItem($this->templateService),
            new EditTemplateItem($this->templateService),
            new DeleteTemplateItem($this->templateService),
            new TableActionsTemplateItem($this->templateService)
        ];
    }

    /**
     * Returns template items for New template
     *
     * @return array
     */
    public function getNewTemplateItems(): array
    {
        return [
            new NewTemplateItem($this->templateService),
            new FilterTemplateItem($this->templateService),
            new FormTemplateItem($this->templateService),
            new ListTemplateItem($this->templateService),
        ];
    }

    /**
     * Returns template items for Edit template
     *
     * @return array
     */
    public function getEditTemplateItems(): array
    {
        return [
            new EditTemplateItem($this->templateService),
            new FilterTemplateItem($this->templateService),
            new FormTemplateItem($this->templateService),
            new ListTemplateItem($this->templateService),
            new DeleteTemplateItem($this->templateService),
        ];
    }

    /**
     * @return DeleteTemplateItem[]
     */
    public function getDeleteTemplateItems(): array
    {
        return [
            new DeleteTemplateItem($this->templateService),
        ];
    }

    /**
     * @return array
     */
    public function getHistoryListTemplateItems(): array
    {
        return [
            new HistoryListTemplateItem($this->templateService),
            new NewsListTemplateItem($this->templateService),
        ];
    }

    /**
     * @return array
     */
    public function getNewsListTemplateItems(): array
    {
        return [
            new HistoryListTemplateItem($this->templateService),
            new NewsListTemplateItem($this->templateService),
        ];
    }
}
