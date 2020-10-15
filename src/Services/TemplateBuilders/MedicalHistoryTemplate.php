<?php

namespace App\Services\TemplateBuilders;

use App\Controller\AppAbstractController;
use App\Entity\Patient;
use App\Repository\PatientRepository;
use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
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
    ];

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
            self::FILTER_CONTENT
        );
    }

    /**
     * Builds list template settings of MedicalHistory controller
     *
     * @param FilterService|null $filterService
     *
     * @return $this|AdminTemplateBuilder
     */
    public function list(?FilterService $filterService = null): AdminTemplateBuilder
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
                                return $value->getAuthUser()->getLastName().' '.$value->getAuthUser()->getFirstName();
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