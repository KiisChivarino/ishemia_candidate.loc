<?php


namespace App\Entity;

class TemplateRoutes
{
    private $routeKey;

    private $itemName;

    /**
     * @return string
     */
    public function getRouteKey()
    {
        return $this->routeKey;
    }

    /**
     * @param string $routeKey
     */
    public function setRouteKey(string $routeKey): void
    {
        $this->routeKey = $routeKey;
    }

    /**
     * @return string
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * @param string $itemName
     */
    public function setItemName(string $itemName): void
    {
        $this->itemName = $itemName;
    }

    /**
     * @param string $routeKey
     * @param string $itemName
     * @return TemplateRoutes
     */
    public function addTemplateRoute(string $routeKey, string $itemName): TemplateRoutes
    {
        $this->setRouteKey($routeKey);
        $this->setItemName($itemName);
        return $this;
    }
}