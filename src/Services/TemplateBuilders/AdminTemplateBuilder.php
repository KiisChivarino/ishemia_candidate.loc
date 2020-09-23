<?php

namespace App\Services\TemplateBuilders;

use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateService;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use App\Services\TemplateItems\TemplateItemsFactory;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AdminTemplateBuilder
 *
 * @package App\Services\TemplateBuilders
 */
abstract class AdminTemplateBuilder extends TemplateService
{
    /** @var string[] Common content */
    protected const COMMON_CONTENT = [];

    /** @var string[] Common FORM_CONTENT */
    protected const FORM_CONTENT = [];

    /** @var string[] Common FORM_SHOW_CONTENT */
    protected const FORM_SHOW_CONTENT = [];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Список записей',
        'title' => 'Список записей',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новая запись',
        'title' => 'Новая запись',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр записи',
        'title' => 'Просмотр записи',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование записи',
        'title' => 'Редактирование записи',
    ];

    /** @var string[] Common FILTER_CONTENT */
    protected const FILTER_CONTENT = [];

    /** @var TemplateItemsFactory $templateItemsFactory */
    private $templateItemsFactory;

    /** @var string[] Common content for templates */
    protected $commonContent;

    /** @var string[] $formContent */
    protected $formContent;

    /** @var string[] $formShowContent */
    protected $formShowContent;

    /** @var string[] $listContent */
    protected $listContent;

    /** @var string[] $newContent */
    protected $newContent;

    /** @var string[] $showContent */
    protected $showContent;

    /** @var string[] $editContent */
    protected $editContent;

    /** @var string[] $filterContent */
    protected $filterContent;

    /**
     * CountryTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct($routeCollection, $className);
        $this->templateItemsFactory = new TemplateItemsFactory();
        $this->addContent(
            self::LIST_CONTENT,
            self::NEW_CONTENT,
            self::SHOW_CONTENT,
            self::EDIT_CONTENT,
            self::FORM_CONTENT,
            self::FORM_SHOW_CONTENT,
            self::COMMON_CONTENT,
            self::FILTER_CONTENT
        );
    }

    /**
     * Builds list template settings
     *
     * @param FilterService|null $filterService
     *
     * @return $this
     */
    public function list(?FilterService $filterService = null): self
    {
        $this->setTemplateItems($this->templateItemsFactory->getListTemplateItems());
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
            ->addContentArray($this->commonContent)
            ->addContentArray($this->listContent);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->addContentArray($this->filterContent);
        return $this;
    }

    /**
     * Builds New template settings
     *
     * @param FilterService|null $filterService
     *
     * @return $this
     */
    public function new(?FilterService $filterService = null): self
    {
        $this->setTemplateItems($this->templateItemsFactory->getNewTemplateItems());
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->addContentArray($this->commonContent)
            ->addContentArray($this->formShowContent)
            ->addContentArray($this->formContent);
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)->addContentArray($this->newContent);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->addContentArray($this->filterContent);
        return $this;
    }

    /**
     * Builds Show template settings
     *
     * @param object|null $entity
     *
     * @return $this
     */
    public function show(?object $entity = null): self
    {
        $this->setTemplateItems($this->templateItemsFactory->getShowTemplateItems());
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->addContentArray($this->commonContent)
            ->addContentArray($this->formShowContent)
            ->addContentArray($this->showContent);
        return $this;
    }

    /**
     * Builds Edit template settings
     *
     * @param object|null $entity
     *
     * @return $this
     */
    public function edit(?object $entity = null): self
    {
        $this->setTemplateItems($this->templateItemsFactory->getEditTemplateItems());
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
            ->addContentArray($this->commonContent)
            ->addContentArray($this->editContent);
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->addContentArray($this->commonContent)
            ->addContentArray($this->formShowContent)
            ->addContentArray($this->formContent);
        return $this;
    }

    /**
     * Add content
     *
     * @param array|null $listContent
     * @param array|null $newContent
     * @param array|null $showContent
     * @param array|null $editContent
     * @param array|null $formContent
     * @param array|null $formShowContent
     * @param array|null $commonContent
     * @param array|null $filterContent
     *
     * @return AdminTemplateBuilder
     */
    protected function addContent(
        ?array $listContent = [],
        ?array $newContent = [],
        ?array $showContent = [],
        ?array $editContent = [],
        ?array $formContent = [],
        ?array $formShowContent = [],
        ?array $commonContent = [],
        ?array $filterContent = []
    ): self {
        $this->commonContent = $commonContent;
        $this->formContent = $formContent;
        $this->formShowContent = $formShowContent;
        $this->listContent = $listContent;
        $this->newContent = $newContent;
        $this->showContent = $showContent;
        $this->editContent = $editContent;
        $this->filterContent = $filterContent;
        return $this;
    }
}