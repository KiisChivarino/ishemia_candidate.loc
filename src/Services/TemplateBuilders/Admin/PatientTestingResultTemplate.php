<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Repository\PatientTestingRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientTestingResultTemplateBuilder
 * prepare template data for patient testing result
 *
 * @package App\Services\TemplateBuilders
 */
class PatientTestingResultTemplate extends AdminTemplateBuilder
{

    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'patientTesting' => 'Обследование',
        'analysis' => 'Анализ',
        'analysisRate' => 'Референтные значения',
        'result' => 'Результат',
        'testingDate' => 'Дата прохождения теста'
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Результаты анализов пациента',
        'title' => 'Результаты анализов пациента',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новые результаты анализа пациента',
        'title' => 'Новые результаты анализа пациента',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Результаты анализа пациента',
        'title' => 'Результаты анализа пациента',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование результатов анализа пациента',
        'title' => 'Редактирование результатов анализа пациента',
    ];

    /** @var string[] Common FILTER_CONTENT */
    protected const FILTER_CONTENT = [
        'patientFilter' => 'Фильтр по пациенту',
        'patientTestingFilter' => 'Фильтр по обследованию пациента',
    ];

    /**
     * PatientTestingResultTemplate constructor.
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
     * Builds list template settings of PatientTestingResult controller
     *
     * @param FilterService|null $filterService
     *
     * @return AppTemplateBuilder
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list();
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)->setIsEnabled(false);
        $filterTemplateItem = $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        'patient',
                        Patient::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getContentValue('patientFilter'),
                            'class' => Patient::class,
                            'required' => false,
                            'choice_label' => 'authUser.lastName',
                        ]
                    ),
                ]
            );
        $patientFilterEntity = $filterTemplateItem->getFilterDataByName('patient')->getEntity();
        $filterTemplateItem->setFilters(
            $filterService,
            [
                new TemplateFilter(
                    'patientTesting',
                    PatientTesting::class,
                    [
                        'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getContentValue('patientTestingFilter'),
                        'class' => PatientTesting::class,
                        'required' => false,
                        'choice_label' => function ($patientTesting) {
                            return (new PatientTestingInfoService())->getPatientTestingInfoString($patientTesting);
                        },
                        'query_builder' => function (PatientTestingRepository $er) use ($patientFilterEntity) {
                            $qb = $er->createQueryBuilder('pt')->where('pt.enabled = true');
                            if ($patientFilterEntity) {
                                $qb
                                    ->leftJoin('pt.medicalHistory', 'mh')
                                    ->andWhere('mh.patient = :patientValue')
                                    ->setParameter('patientValue', $patientFilterEntity);
                            }
                            return $qb;
                        },
                    ]
                ),
            ]
        );
        return $this;
    }
}