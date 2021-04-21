<?php

namespace App\Services\Template;

use Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;

/**
 * Class TemplateService
 * управление общими шаблонами crud админки
 *
 * @package App\Services\Template
 */
class TemplateService
{
    /** @var string Path to routes config file */
    private const YAML_ROUTES_PATH = '../config/services/template/routes.yaml';

    /** @var array $routes Массив с роутами контроллера */
    private $routes;

    /** @var string Путь к каталогу с шаблонами контроллера */
    private $templatePath;

    /** @var string Путь к каталогу с общими шаблонами crud админки */
    private $commonTemplatePath;

    /** @var array $items Template items */
    private $items;

    /** @var string $resirectRouteName Названия роута для редиректа */
    private $redirectRouteName;

    /** @var array $redirectRouteParameters Parameters of route for redirect */
    private $redirectRouteParameters;

    /**
     * TemplateService constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     * @param string $commonTemplatePath
     * @param string $redirectRouteName
     */
    public function __construct(RouteCollection $routeCollection, string $className, string $commonTemplatePath, string $redirectRouteName)
    {
        $this->routes = $this->getRoutesFromRouteCollection($routeCollection, $className);
        $this->templatePath = $className::TEMPLATE_PATH;
        $this->items = [];
        $this->setCommonTemplatePath($commonTemplatePath);
        $this->setRedirectRoute($this->getRoute($redirectRouteName) ? $this->getRoute($redirectRouteName) : $this->getRoute('index'));
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
        $yaml = Yaml::parseFile(self::YAML_ROUTES_PATH);
        if (isset($yaml['parameters'])) {
            $routes['index'] = $yaml['parameters']['index'];
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
    public function getCommonTemplatePath(): string
    {
        return $this->commonTemplatePath;
    }

    /**
     * @param string $commonTemplatePath
     * @return $this
     */
    public function setCommonTemplatePath(string $commonTemplatePath): TemplateService
    {
        $this->commonTemplatePath = $commonTemplatePath;
        return $this;
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
     * @param string $route
     * @return string|null
     */
    public function getRoute(string $route): ?string
    {
        return array_key_exists($route, $this->getRoutes()) ? $this->getRoutes()[$route] : $this->redirectRouteName;
    }

    /**
     * Get template item
     *
     * @param string|null $itemName
     *
     * @return mixed|null
     */
    public function getItem(?string $itemName): ?TemplateItem
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

    /**
     * Sets redirect route
     * @param string $redirectRouteName
     * @param array $redirectRouteParameters
     * @return TemplateService
     */
    public function setRedirectRoute(string $redirectRouteName, array $redirectRouteParameters = []): self
    {
        $this->redirectRouteParameters = $redirectRouteParameters;
        $this->redirectRouteName = $redirectRouteName;

        return $this;
    }

    /**
     * Returns name of route for redirect
     * @return string
     */
    public function getRedirectRouteName(): string
    {
        return $this->redirectRouteName;
    }

    /**
     * Returns parameters of route for redirect
     * @return array|null
     */
    public function getRedirectRouteParameters(): ?array
    {
        return $this->redirectRouteParameters;
    }

    /**
     * Returns template file name with path
     * @param string $templateName
     * @param string $projectDir
     * @return string
     * @throws Exception
     */
    public function getTemplateFullName(string $templateName, string $projectDir): string
    {
        $templatePathFullName = $this->templatePath . $templateName . '.html.twig';
        $commonPathFullName = $this->getCommonTemplatePath() . $templateName . '.html.twig';
        if (is_file($projectDir . '/templates/' . $commonPathFullName)) {
            return $commonPathFullName;
        } elseif (is_file($projectDir . '/templates/' . $templatePathFullName)) {
            return $templatePathFullName;
        } else {
            throw new Exception('Файл шаблона не найден! Искали по адресам: "' . $templatePathFullName . '", "' . $commonPathFullName . '"');
        }
    }

    /**
     * Sets redirect route parameters
     * $redirectRouteParameters =
     * [
     * 'paramName' => 'entityObject'
     * ]
     * @param array $redirectRouteParameters
     */
    public function setRedirectRouteParameters(array $redirectRouteParameters){
        $this->redirectRouteParameters = $redirectRouteParameters;
    }
}