<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Entity\PatientTesting;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\TemplateBuilders\Admin\PatientTestingResultTemplate;
use App\Services\TemplateBuilders\Admin\PatientTestingTemplate;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class EditPatientTestingTemplate
 * @package App\Services\TemplateBuilders\DoctorOffice
 */
class EditPatientTestingTemplate extends DoctorOfficeTemplateBuilder
{
    /** @var string[] Common form content for edit templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование обследования',
        'title' => 'Редактирование обследования',
        'discharge_epicrisis' => 'Редактирование выписных эпикризов',
    ];

    /** @var array Common form content for form templates */
    protected const FORM_CONTENT = [
        'analysisDate' => PatientTestingTemplate::COMMON_CONTENT['analysisDate'],
        'processed' => PatientTestingTemplate::COMMON_CONTENT['processed'],
    ];

    /** @var array Common content for form and edit templates */
    protected const FORM_EDIT_CONTENT = [
        'analysis' => PatientTestingResultTemplate::COMMON_CONTENT['analysis'],
        'analysisRate' => PatientTestingResultTemplate::COMMON_CONTENT['analysisRate'],
        'result' => PatientTestingResultTemplate::COMMON_CONTENT['result'],
    ];

    /**
     * EditPatientTestingTemplate constructor.
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
     * @param object|null $entity
     * @return $this|AppTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit($entity);
        /** @var PatientTesting $patientTesting */
        $patientTesting = $entity;
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
            ->setContent('h1', PatientTestingInfoService::getPatientTestingInfoString($patientTesting))
            ->setPath($this->getTemplatePath())
            ->setContents(self::FORM_EDIT_CONTENT);
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->setPath($this->getTemplatePath())
            ->setContents(self::FORM_EDIT_CONTENT);
        return $this;
    }
}