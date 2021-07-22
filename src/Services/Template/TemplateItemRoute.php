<?php

namespace App\Services\Template;
/**
 * Class TemplateItemRoute
 * route and parameters for template item
 * by default will be route with name of item (if exists in controller) and parameter "id"
 * @package App\Services\Template
 */
class TemplateItemRoute
{
    private $routeName = '';

    private $routeParams = [];

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     * @return TemplateItemRoute
     */
    public function setRouteName(string $routeName): self
    {
        $this->routeName = $routeName;
        return $this;
    }

    /**
     * @return array
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * @param array $routeParams
     */
    public function setRouteParams(array $routeParams): void
    {
        $this->routeParams = $routeParams;
    }
}