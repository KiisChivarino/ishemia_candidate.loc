<?php

namespace App\Services\Template;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class TemplateService
 * управление общими шаблонами crud админки
 *
 * @package App\Services\Template
 */
class TemplateService
{
    /** @var string Путь к общим шаблонам crud админки */
    public const DEFAULT_COMMON_TEMPLATE_PATH = 'admin/common_template/';

    /** @var array $routes Массив с роутами контроллера */
    private $routes;

    /** @var string Путь к каталогу с шаблонами контроллера */
    private $templatePath;

    /** @var string Путь к каталогу с общими шаблонами crud админки */
    private $commonTemplatePath;

    /** @var array $items Template items */
    private $items;

    /**
     * TemplateService constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        $this->routes = $this->getRoutesFromRouteCollection($routeCollection, $className);
        $this->templatePath = $className::TEMPLATE_PATH;
        $this->commonTemplatePath = self::DEFAULT_COMMON_TEMPLATE_PATH;
        $this->items = [];
    }

    /**
     * Returns routes of controller
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Возвращает массив вида [<имя метода>=><имя роута>...]
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     *
     * @return array
     */
    private function getRoutesFromRouteCollection(RouteCollection $routeCollection, string $className): array
    {
        $routes = [];
        /**
         * @var string $key
         * @var Route $value
         */
        foreach ($routeCollection->all() as $key => $value) {
            $routeInfo = explode('::', $value->getDefault('_controller'));
            if ($routeInfo[0] == $className) {
                $routes[$routeInfo[1]] = $key;
            }
        }
        return $routes;
    }

    /**
     * Получить путь к шаблонам контроллера
     *
     * @return string
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * Set template path
     *
     * @param string $templatePath
     *
     * @return $this
     */
    public function setTemplatePath(string $templatePath): self
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * Get path of common templates
     *
     * @return string
     */
    public function getCommonTemplatePath()
    {
        return $this->commonTemplatePath;
    }

    /**
     * Returns template items
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Добавляет переменную в шаблоны или переопределяет значение по умолчанию
     *
     * @param TemplateItem $templateItem
     *
     * @return TemplateService
     */
    public function setTemplateItem(TemplateItem $templateItem): self
    {
        $this->items[$templateItem->getName()] = $templateItem;
        return $this;
    }

    /**
     * Get route of controller by route name
     *
     * @param string $route
     *
     * @return mixed|null
     */
    public function getRoute(string $route)
    {
        return array_key_exists($route, $this->getRoutes()) ? $this->getRoutes()[$route] : null;
    }

    /**
     * Get template item
     *
     * @param string $itemName
     *
     * @return mixed|null
     */
    public function getItem(string $itemName): ?TemplateItem
    {
        return array_key_exists($itemName, $this->getItems()) ? $this->getItems()[$itemName] : null;
    }

    /**
     * Set template items into template service object
     *
     * @param array $templateItems
     */
    public function setTemplateItems(array $templateItems): void
    {
        foreach ($templateItems as $item) {
            $this->setTemplateItem($item);
        }
    }
}