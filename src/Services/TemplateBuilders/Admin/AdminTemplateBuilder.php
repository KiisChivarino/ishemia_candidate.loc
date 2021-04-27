<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\TemplateBuilders\AppTemplateBuilder;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AdminTemplateBuilder
 *
 * @package App\Services\TemplateBuilders
 */
abstract class AdminTemplateBuilder extends AppTemplateBuilder
{
    /** @var string Default redirect route name */
    public const DEFAULT_REDIRECT_ROUTE_NAME = 'show';

    /** @var string Путь к общим шаблонам crud админки */
    public const DEFAULT_COMMON_TEMPLATE_PATH = 'admin/common_template/';

    /**
     * CountryTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct(
            $routeCollection,
            $className,
            self::DEFAULT_COMMON_TEMPLATE_PATH,
            self::DEFAULT_REDIRECT_ROUTE_NAME
        );
    }
}