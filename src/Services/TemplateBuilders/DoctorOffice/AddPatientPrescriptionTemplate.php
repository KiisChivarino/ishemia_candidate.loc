<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\PrescriptionTemplate;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\ListTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AddPatientPrescriptionTemplate
 * @package App\Services\TemplateBuilders\DoctorOffice
 */
class AddPatientPrescriptionTemplate extends DoctorOfficeTemplateBuilder
{
    protected const FORM_CONTENT = [
        'medicineName' => 'Название лекарства',
        'instruction' => 'Инструкция по применению',
        'dateBegin' => 'Планируемая дата начала приема лекарства',
    ];

    protected const SHOW_CONTENT = [
        'title' => 'Просмотр назначения',
        'h2' => 'Просмотр назначения',
        'createdTime' => 'Дата и время создания назначения',
        'doctor' => 'Врач',
    ];

    /**
     * AddPatientPrescriptionTemplate constructor.
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
            PrescriptionTemplate::ENTITY_CONTENT
        );
    }

    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new($filterService);
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->setIsEnabled(false);
        return $this;
    }
}