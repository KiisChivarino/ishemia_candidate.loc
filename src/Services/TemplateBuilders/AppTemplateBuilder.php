<?php

namespace App\Services\TemplateBuilders;

use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateService;
use App\Services\TemplateBuilders\Admin\AdminTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use App\Services\TemplateItems\TemplateItemsFactory;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AppTemplateBuilder
 * @package App\Services\TemplateBuilders
 */
class AppTemplateBuilder extends TemplateService
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

    /** @var string[] Common ENTITY_CONTENT */
    protected const ENTITY_CONTENT = [
        'entity' => 'Запись',
    ];

    /** @var TemplateItemsFactory $templateItemsFactory */
    protected $templateItemsFactory;

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

    /** @var string[] $entityContent */
    protected $entityContent;

    /**
     * CountryTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     * @param string $defaultCommonTemplatePath
     * @param string $defaultRedirectRouteName
     * @throws Exception
     */
    public function __construct(
        RouteCollection $routeCollection,
        string $className,
        string $defaultCommonTemplatePath,
        string $defaultRedirectRouteName
    )
    {
        parent::__construct(
            $routeCollection,
            $className,
            $defaultCommonTemplatePath,
            $defaultRedirectRouteName
        );
        $this->templateItemsFactory = new TemplateItemsFactory($this);
        $this->addContent(
            self::LIST_CONTENT,
            self::NEW_CONTENT,
            self::SHOW_CONTENT,
            self::EDIT_CONTENT,
            self::FORM_CONTENT,
            self::FORM_SHOW_CONTENT,
            self::COMMON_CONTENT,
            self::FILTER_CONTENT,
            self::ENTITY_CONTENT
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
            ->addContentArray($this->listContent)
            ->addContentArray($this->entityContent);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->addContentArray($this->filterContent);

        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->getTemplateItemRoute()
            ->setRouteName($this->getRoutes()['show']);
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
            ->getTemplateItemRoute()
            ->setRouteName($this->getRoutes()['edit']);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->getTemplateItemRoute()
            ->setRouteName($this->getRoutes()['delete']);

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
            ->addContentArray($this->showContent)
            ->addContentArray($this->entityContent);
        if ($this->isMethodGetIdExists($entity)) {
            $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
                ->getTemplateItemRoute()->setRouteParams(['id'=>$entity->getId()]);
            $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
                ->getTemplateItemRoute()->setRouteParams(['id' => $entity->getId()]);
        }
        return $this;
    }

    /**
     * Check is method getId exists for entity
     * @param object|null $entity
     * @return bool
     */
    public function isMethodGetIdExists(?object $entity = null): bool
    {
        return is_object($entity)&& method_exists($entity, 'getId');
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
            ->addContentArray($this->editContent)
            ->addContentArray($this->entityContent);
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->addContentArray($this->commonContent)
            ->addContentArray($this->formShowContent)
            ->addContentArray($this->formContent);
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
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->addContentArray($this->newContent)
            ->addContentArray($this->entityContent);
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->addContentArray($this->commonContent)
            ->addContentArray($this->formShowContent)
            ->addContentArray($this->formContent);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->addContentArray($this->filterContent);
        return $this;
    }

    /**
     * Builds Delete template settings
     *
     * @return $this
     */
    public function delete(): self
    {
        $this->setTemplateItems($this->templateItemsFactory->getDeleteTemplateItems());
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->addContentArray($this->entityContent);
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
     * @param array|null $entityContent
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
        ?array $filterContent = [],
        ?array $entityContent = []
    ): self
    {
        $this->commonContent = $commonContent;
        $this->formContent = $formContent;
        $this->formShowContent = $formShowContent;
        $this->listContent = $listContent;
        $this->newContent = $newContent;
        $this->showContent = $showContent;
        $this->editContent = $editContent;
        $this->filterContent = $filterContent;
        $this->entityContent = $entityContent;
        return $this;
    }
}
