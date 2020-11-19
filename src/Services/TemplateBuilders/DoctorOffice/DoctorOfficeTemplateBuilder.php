<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\TemplateBuilders\AppTemplateBuilder;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class DoctorOfficeTemplateBuilder
 * @package App\Services\TemplateBuilders\DoctorOffice
 */
abstract class DoctorOfficeTemplateBuilder extends AppTemplateBuilder
{
    /** @var string Default redirect route name */
    public const DEFAULT_REDIRECT_ROUTE_NAME = 'index';

    /** @var string Путь к общим шаблонам crud админки */
    public const DEFAULT_COMMON_TEMPLATE_PATH = 'doctorOffice/common_template/';

    /**
     * DoctorOfficeTemplateBuilder constructor.
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