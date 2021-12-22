<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Patient;
use App\Repository\PatientRepository;
use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class MedicalHistoryTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class MedicalHistoryTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    public const COMMON_CONTENT = [
        'patientFio' => 'ФИО пациента',
        'dateBegin' => 'Дата создания',
        'dateEnd' => 'Дата завершения',
        'mainDisease' => 'Основное заболевание',
        'treatmentNotCompleted' => 'Лечение не завершено',
        'MKBCode' => ClinicalDiagnosisTemplate::COMMON_CONTENT['MKBCode'],
        'text' => ClinicalDiagnosisTemplate::COMMON_CONTENT['text'],
    ];

    /** @var string[] FORM_SHOW_CONTENT */
    public const FORM_SHOW_CONTENT = [
        'mainDiseasePlaceholder' => 'Выберите заболевание',
        'backgroundDiseases' => 'Фоновые заболевания',
        'backgroundDiseasesPlaceholder' => 'Выберите фоновые заболевания',
        'complications' => 'Осложнения основного заболевания',
        'complicationsPlaceholder' => 'Выберите осложнения',
        'concomitantDiseases' => 'Сопутствующие заболевания',
        'concomitantDiseasesPlaceholder' => 'Выберите сопутствующие заболевания',
        'diseaseHistory' => 'Анамнез заболевания',
        'lifeHistory' => 'Анамнез жизни',
        'dischargeEpicrisis' => 'Выписные эпикризы',
        'clinicalDiagnosis' => 'Клинический диагноз',
        'MKBCodePlaceholder' => ClinicalDiagnosisTemplate::FORM_CONTENT['MKBCodePlaceholder'],
        'dateBeginNotFound' => 'Дата начала лечения не найдена!',
        'MKBNotFound' => ClinicalDiagnosisTemplate::COMMON_CONTENT['MKBNotFound'],
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Список историй болезни',
        'title' => 'Список историй болезни',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление истории болезни',
        'title' => 'Добавление истории болезни',
    ];

    /** @var string[] Common SHOW_CONTENT */
    public const SHOW_CONTENT = [
        'h1' => 'Просмотр истории болезни',
        'title' => 'Просмотр истории болезни',
        'medicalRecords' => 'Записи в историю болезни',
        'addMedicalRecord' => 'Запись в историю болезни',
        'addPatientTesting' => 'Обследование',
        'patientTestings' => 'Обследования пациента',
        'addPrescription' => 'Назначение',
        'prescriptions' => 'Назначения',
        'addPatientAppointment' => 'Прием пациента',
        'patientAppointments' => 'Приемы пациентов',
        'addNotification' => 'Уведомление',
        'notifications' => 'Уведомления',
        'addPrescriptionMedicine' => 'Назначение лекарства',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование истории болезни',
        'title' => 'Редактирование истории болезни',
    ];

    /** @var string[] Common FILTER_CONTENT */
    protected const FILTER_CONTENT = [
        'patientFilter' => 'Фильтр по пациенту',
    ];

    /** @var string[] Common ENTITY_CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'История болезни',
    ];

    /**
     * MedicalHistoryTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     * @throws Exception
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
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)->setPath($this->getTemplatePath());
        return $this;
    }

    /**
     * @param object|null $entity
     * @return $this|AppTemplateBuilder
     * @throws Exception
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->setRedirectRouteParameters([
            'id' => $entity->getId(),
        ]);
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)->setPath($this->getTemplatePath());
        return $this;
    }

    /**
     * Builds list template settings of MedicalHistory controller
     *
     * @param FilterService|null $filterService
     *
     * @param array|null $itemsWithRoutes
     * @return AppTemplateBuilder
     * @throws Exception
     */
    public function list(?FilterService $filterService = null, ?array $itemsWithRoutes = null): AppTemplateBuilder
    {
        parent::list();
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)->setIsEnabled(false);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['PATIENT'],
                        Patient::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                                ->getContentValue('patientFilter'),
                            'class' => Patient::class,
                            'required' => false,
                            'choice_label' => function ($value) {
                                return $value->getAuthUser()->getLastName() . ' ' . $value->getAuthUser()->getFirstName();
                            },
                            'query_builder' => function (PatientRepository $er) {
                                return $er->createQueryBuilder('p')
                                    ->leftJoin('p.AuthUser', 'au')
                                    ->where('au.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
}