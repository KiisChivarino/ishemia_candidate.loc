<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateBuilders\Admin\AuthUserTemplate;
use App\Services\TemplateBuilders\Admin\PatientTemplate;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class MedicalHistoryTemplate
 *
 * @package App\Services\TemplateBuilders\PatientOffice
 */
class PersonalDataTemplate extends DoctorOfficeTemplateBuilder
{
    /** @var string[] Common form content for edit templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Личные данные',
        'title' => 'Личные данные',
    ];

    /** @var string[] Common ENTITY_CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => \App\Services\TemplateBuilders\Admin\MedicalHistoryTemplate::ENTITY_CONTENT['entity'],
    ];

    /**
     * MedicalHistoryTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct($routeCollection, $className);
        $this->addContent(
            null,
            null,
            null,
            self::EDIT_CONTENT,
            null,
            null,
            null,
            null,
            self::ENTITY_CONTENT
        );
    }

    /**
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->setCommonTemplatePath(($this->getTemplatePath()));
        $this
            ->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->setPath($this->getTemplatePath())
            ->addContentArray(
                array_merge(
                    AuthUserTemplate::COMMON_CONTENT,
                    AuthUserTemplate::FORM_CONTENT,
                    AuthUserTemplate::FORM_SHOW_CONTENT,
                    PatientTemplate::COMMON_CONTENT,
                    PatientTemplate::FORM_SHOW_CONTENT,
                    PatientTemplate::FORM_CONTENT
                )
            );
        return $this;
    }
}