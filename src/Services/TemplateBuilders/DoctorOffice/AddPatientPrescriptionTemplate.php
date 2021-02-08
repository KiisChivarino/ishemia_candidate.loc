<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\TemplateBuilders\Admin\PrescriptionTemplate;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AddPatientPrescriptionTemplate
 * @package App\Services\TemplateBuilders\DoctorOffice
 */
class AddPatientPrescriptionTemplate extends DoctorOfficeTemplateBuilder
{
    protected const SHOW_CONTENT = [
        'title' => 'Просмотр назначения',
        'h1' => 'Просмотр назначения',
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
}