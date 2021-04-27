<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Repository\MedicalHistoryRepository;
use App\Repository\PatientRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class PatientTestingTemplate
 * prepare template data for patient testing list
 *
 * @package App\Services\TemplateBuilders
 */
class PatientTestingTemplate extends AdminTemplateBuilder
{

    /** @var string[] Common content for PatientTesting templates */
    public const COMMON_CONTENT = [
        'analysisGroup' => 'Группа анализов',
        'analysisDate' => 'Проведено',
        'processed' => 'Обработано врачом',
        'patientTestingFiles'=> 'Сканкопии результатов анализов',
        'resultData' => 'Данные результатов обследования',
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Анализы пациентов',
        'title' => 'Список анализов пациентов',
        'fio' => 'ФИО',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новый анализ пациента',
        'title' => 'Новый анализ пациента',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Обследование пациента',
        'title' => 'Просмотр анализа пациента',
        'analysisResultsLink' => 'Результаты анализов',
        'isByPlan' => 'По плану обследований',
        'planTesting' => 'План обследований',
        'prescriptionTesting' => PrescriptionTestingTemplate::ENTITY_CONTENT['entity'],
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование анализа пациента',
        'title' => 'Редактирование анализа пациента',
    ];

    /** @var string[] Common FILTER_CONTENT */
    protected const FILTER_CONTENT = [
        'patient' => 'Фильтр по пациенту',
        'medicalHistory' => 'Фильтр по истории болезни',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Анализ пациента',
    ];

    /** @var string[] Common FORM_SHOW_CONTENT */
    public const FORM_SHOW_CONTENT = [
        'resultData' => 'Данные результатов обследования',
    ];

    /**
     * PatientTestingTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        RouteCollection $routeCollection,
        string $className,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($routeCollection, $className, $authorizationChecker);
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
     * @return $this|AdminTemplateBuilder
     */
    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new();
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)->setPath($this->getTemplatePath());
        return $this;
    }

    /**
     * @param object|null $entity
     *
     * @return $this|AdminTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)->setPath($this->getTemplatePath());
        return $this;
    }

    /**
     * Builds list template settings of PatientTesting controller
     *
     * @param FilterService|null $filterService
     *
     * @return AppTemplateBuilder
     * @throws Exception
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list();
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->setIsEnabled(false);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['PATIENT'],
                        Patient::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getContentValue('patient'),
                            'class' => Patient::class,
                            'required' => false,
                            'choice_label' => 'AuthUser.lastName',
                            'query_builder' => function (PatientRepository $er) {
                                return $er->createQueryBuilder('p')
                                    ->leftJoin('p.AuthUser', 'a')
                                    ->where('a.enabled = true');
                            },
                        ]
                    ),
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY'],
                        MedicalHistory::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getContentValue('medicalHistory'),
                            'class' => MedicalHistory::class,
                            'required' => false,
                            'choice_label' => function (MedicalHistory $value) {
                                return (new AuthUserInfoService())->getFIO($value->getPatient()->getAuthUser()).': '.$value->getDateBegin()->format('d.m.Y');
                            },
                            'query_builder' => function (MedicalHistoryRepository $er) {
                                return $er->createQueryBuilder('mh')
                                    ->leftJoin('mh.patient', 'p')
                                    ->leftJoin('p.AuthUser', 'a')
                                    ->where('a.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
}