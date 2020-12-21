<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\AuthUserTemplate;
use App\Services\TemplateBuilders\Admin\MedicalHistoryTemplate;
use App\Services\TemplateBuilders\Admin\PatientTemplate;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class CreateNewPatientTemplate
 *
 * @package App\Services\TemplateBuilders\DoctorOffice
 */
class CreateNewPatientTemplate extends DoctorOfficeTemplateBuilder
{
    public const NEW_CONTENT = [
        'title' => 'Новый пациент',
        'h1' => 'Новый пациент',
    ];
    public const FORM_CONTENT = [
        'inputMainDisease' => 'Свое название основного заболевания',
    ];

    /** @var string[] Common ENTITY_CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => PatientTemplate::ENTITY_CONTENT['entity'],
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
     * @return $this|AppTemplateBuilder
     */
    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new();
//        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)->setPath($this->getTemplatePath());
        $this->setCommonTemplatePath($this->getTemplatePath());
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->addContentArray(
                array_merge(
                    AuthUserTemplate::FORM_CONTENT,
                    AuthUserTemplate::FORM_SHOW_CONTENT,
                    MedicalHistoryTemplate::COMMON_CONTENT,
                    MedicalHistoryTemplate::FORM_SHOW_CONTENT,
                    PatientTemplate::COMMON_CONTENT,
                    PatientTemplate::FORM_SHOW_CONTENT,
                    PatientTemplate::FORM_CONTENT
                )
            );
        return $this;
    }
}