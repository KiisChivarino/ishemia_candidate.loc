<?php

namespace App\Services\TemplateBuilders\PatientOffice;

use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientTestingTemplate
 *
 * @package App\Services\TemplateBuilders\PatientOffice
 */
class PatientTestingTemplate extends PatientOfficeTemplateBuilder
{

    /** @var string[] New patient testing content */
    public const NEWS_LIST_CONTENT = [
        'title' => 'Новые обследования',
        'h1' => 'Новые обследования',
        'submitResult' => 'Подтвердить',
    ];

    /** @var string[] Old patient testing content */
    public const HISTORY_LIST_CONTENT = [
        'title' => 'История обследований',
        'h1' => 'История обследований',
    ];

    /**
     * @param RouteCollection $routeCollection
     * @param string $className
     * @throws Exception
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct($routeCollection, $className);
        $this->addPatientOfficeContent(
            self::COMMON_CONTENT,
            self::HISTORY_LIST_CONTENT,
            self::NEWS_LIST_CONTENT
        );
    }
}