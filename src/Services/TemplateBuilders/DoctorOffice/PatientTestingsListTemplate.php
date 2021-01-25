<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\AnalysisGroup;
use App\Entity\PatientTesting;
use App\Repository\AnalysisGroupRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\Template\TemplateFilter;
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

class PatientTestingsListTemplate extends DoctorOfficeTemplateBuilder
{
    /** @var string[] Common content for patient templates */
    protected const COMMON_CONTENT = [
        'insuranceNumber' => 'Номер страховки',
        'dateBirth' => 'Дата рождения',
        'dateStartOfTreatment' => 'Начало гестации',
        'phone' => 'Телефон',
        'diagnoses' => 'Диагнозы',
        'unprocessedTestings' => 'Показатели',
        'analysisGroup' => 'Группа анализов',
        'analysisDate' => 'Дата проведения обследования',
        'processed' => 'Обработано',
        'resultData' => 'Результаты обследования'
    ];

    /** @var string[] Common FORM_CONTENT */
    protected const FORM_CONTENT = [];

    /** @var string[] Common FORM_SHOW_CONTENT */
    protected const FORM_SHOW_CONTENT = [];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Список обследований',
        'h1NoProcessed' => 'Список необработанных обследований',
        'title' => 'Список записей',
        'fio' => 'ФИО',
        'age' => 'Возраст',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новая запись',
        'title' => 'Новая запись',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр записи',
        'title' => 'Просмотр записи',
    ];

    /** @var array Common content for form and edit templates */
    protected const FORM_EDIT_CONTENT = [
        'analysis' => PatientTestingResultTemplate::COMMON_CONTENT['analysis'],
        'analysisRate' => PatientTestingResultTemplate::COMMON_CONTENT['analysisRate'],
        'result' => PatientTestingResultTemplate::COMMON_CONTENT['result'],
    ];
    /** @var string[] Common FILTER_CONTENT */
    protected const FILTER_CONTENT = [
        'label' => 'Фильтр по пациенту',
    ];

    /** @var string[] Common ENTITY_CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Список пациентов',
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
            ->setPath($this->getTemplatePath())
            ->setContents(self::FORM_EDIT_CONTENT);
        $this->setCommonTemplatePath($this->getTemplatePath());
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->setIsEnabled(false);
        return $this;
    }
}