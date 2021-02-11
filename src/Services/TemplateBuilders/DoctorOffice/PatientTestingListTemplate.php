<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Controller\DoctorOffice\EditPatientTestingController;
use App\Entity\AnalysisGroup;
use App\Entity\PatientTesting;
use App\Repository\AnalysisGroupRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\Admin\AnalysisGroupTemplate;
use App\Services\TemplateBuilders\Admin\PatientTestingResultTemplate;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientTestingsListTemplate
 * @package App\Services\TemplateBuilders\DoctorOffice
 */
class PatientTestingListTemplate extends DoctorOfficeTemplateBuilder
{
    /** @var string[] Common content for patient templates */
    protected const COMMON_CONTENT = [
        'analysisDate' => 'Дата проведения обследования',
        'processed' => 'Обработано',
        'resultData' => 'Результаты обследования',
        'analysisGroup' => AnalysisGroupTemplate::ENTITY_CONTENT['entity']
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'title' => 'Список обследований',
    ];

    /** @var array Common content for form and edit templates */
    protected const FORM_EDIT_CONTENT = [
        'analysis' => PatientTestingResultTemplate::COMMON_CONTENT['analysis'],
        'analysisRate' => PatientTestingResultTemplate::COMMON_CONTENT['analysisRate'],
        'result' => PatientTestingResultTemplate::COMMON_CONTENT['result'],
    ];
    /** @var string[] Common FILTER_CONTENT */
    protected const FILTER_CONTENT = [
        'label' => 'Фильтр по группе анализов',
    ];

    /** @var string[] Common ENTITY_CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Список обследований',
    ];

    /**
     * PatientListTemplate constructor.
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
     * @return AppTemplateBuilder
     * @throws Exception
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list();
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
            ->setPath($this->getTemplatePath());
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->setIsEnabled(false);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->setIsEnabled(false);
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)->setIsEnabled(false);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setContent(
                AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP'],
                $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getContentValue('label')
            )
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP'],
                        AnalysisGroup::class,
                        [
                            'class' => AnalysisGroup::class,
                            'required' => false,
                            'choice_label' => 'name',
                            'label' => false,
                            'query_builder' => function (AnalysisGroupRepository $er) {
                                return $er->createQueryBuilder('a')
                                    ->where('a.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
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
            ->setPath(EditPatientTestingController::TEMPLATE_PATH)
            ->setContents(self::FORM_EDIT_CONTENT);
        $this->setCommonTemplatePath($this->getTemplatePath());
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->setIsEnabled(false);
        return $this;
    }
}