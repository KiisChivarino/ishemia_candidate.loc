<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Entity\Patient;
use App\Entity\PatientSMS;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Omines\DataTablesBundle\DataTableState;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class DataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class ReceivedSmsFromPatientDataTableService extends DoctorOfficeDatatableService
{

    /**
     * DataTableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     * @param UrlGeneratorInterface $router
     * @param EntityManagerInterface $em
     */
    public function __construct(
        DataTableFactory $dataTableFactory,
        UrlGeneratorInterface $router,
        EntityManagerInterface $em
    )
    {
        parent::__construct($dataTableFactory, $router, $em);
    }

    /**
     * Таблица принятых смс от пациента
     *
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array|null $filters
     * @param array $options
     *
     * @return DataTable
     * @throws Exception
     */
    public function getTable(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem,
        ?array $filters,
        array $options
    ): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'patient', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('patient'),
                    'field' => 'aU.lastName',
                    'orderable' => true,
                    'searchable' => false,
                    'orderField' => 'u.lastName',
                    'render' => function (string $data, PatientSMS $patientSMS) {
                        /** @var Patient $patient */
                        $patient = $patientSMS->getPatient();
                        return $patient
                            ? $this->getLink(
                                (new AuthUserInfoService())
                                    ->getFIO($patient->getAuthUser()),
                                $patient->getId(),
                                'doctor_medical_history')
                            : '';
                    }
                ]
            )
            ->add(
                'phone', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('phone'),
                    'render' => function (string $data, PatientSMS $patientSMS) {
                        /** @var Patient $patient */
                        $patient = $patientSMS->getPatient();
                        return (new AuthUserInfoService())->getPhone($patient->getAuthUser());
                    }
                ]
            )
            ->add(
                'text', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('text'),
                ]
            )
            ->add(
                'textSearch', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('text'),
                    'field' => 'upper(pS.text)',
                    'searchable' => true,
                    'visible' => false
                ]
            )
            ->add(
                'created_at', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('createdAt'),
                    'searchable' => false,
                    'format' => 'd.m.Y H:m',
                ]
            )
            ->add(
                'isProcessed', BoolColumn::class, [
                    'label' => $listTemplateItem->getContentValue('isProcessed'),
                    'searchable' => false,
                    'render' => function (string $data, PatientSMS $patientSMS) {
                        return $patientSMS->getIsProcessed()
                            ? '<div style="background-color: #9eff9c">Да</div>'
                            : '<div style="background-color: #ff9c9c">Нет</div>';
                    },
                ]
            )
            ->add(
                'operations', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('operations'),
                    'render' => function (string $data, PatientSMS $patientSMS) {
                        return
                            !$patientSMS->getIsProcessed()
                                ?
                                '<button data-href="'
                                . $this->router->generate(
                                    'process_sms_api',
                                    [
                                        'patientSmsId' => $patientSMS->getId()
                                    ]
                                )
                                . '" data-name="'
                                . AuthUserInfoService::getFIO(
                                    $patientSMS->getPatient()->getAuthUser(),
                                    true
                                )
                                . '" class="button main-button processPatientSMS">Прочитано</button>'
                                : "";
                    }
                ]
            );

        $patientId = $options['patient']->getId();
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PatientSMS::class,
                    'query' => function (QueryBuilder $builder) use ($patientId) {
                        $builder
                            ->select('pS')
                            ->from(PatientSMS::class, 'pS')
                            ->leftJoin('pS.patient', 'p')
                            ->leftJoin('p.AuthUser', 'aU')
                            ->andWhere('p.id = :val')
                            ->setParameter('val', $patientId);
                    },
                    'criteria' => $this->criteriaSearch(),
                ]
            );
    }

    /**
     * Реализация регистронезависимого поиска по дататейблу. Нужно добавить в createAdapter параметр
     * 'criteria' => $this->criteriaSearch(). Далее к полям для поиска нужно добавить следующие параметры:
     * 'field' => 'upper(Название столба в БД с алиасом)',
     * 'searchable' => true,
     *
     * @return array
     */
    protected function criteriaSearch(): array
    {
        return [
            function (QueryBuilder $queryBuilder, DataTableState $state) {
                $state->setGlobalSearch(mb_strtoupper($state->getGlobalSearch()));
            },
            new SearchCriteriaProvider(),
        ];
    }
}