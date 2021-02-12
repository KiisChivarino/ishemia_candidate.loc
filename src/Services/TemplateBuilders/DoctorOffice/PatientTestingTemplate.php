<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\ListTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientTestingTemplate
 * prepare template data for patient testing list
 *
 * @package App\Services\TemplateBuilders
 */
class PatientTestingTemplate extends DoctorOfficeTemplateBuilder
{

    /** @var string[] Common content for PatientTesting templates */
    public const COMMON_CONTENT = [
        'analysisGroup' => 'Группа анализов',
        'plannedDate' => 'Запланировано',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление обследования',
        'title' => 'Добавление обследования',
    ];

    /**
     * PatientTestingTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct($routeCollection, $className);
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
     * @param FilterService|null $filterService
     *
     * @return $this|DoctorOfficeTemplateBuilder
     */
    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new();
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->setIsEnabled(false);
        return $this;
    }
}