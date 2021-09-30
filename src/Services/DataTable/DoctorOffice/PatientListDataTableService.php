<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Entity\Prescription;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class DataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class PatientListDataTableService extends DoctorOfficeDatatableService
{
    /** @var AuthUserInfoService */
    private $authUserInfoService;

    /**
     * DataTableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     * @param UrlGeneratorInterface $router
     * @param AuthUserInfoService $authUserInfoService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        DataTableFactory $dataTableFactory,
        UrlGeneratorInterface $router,
        EntityManagerInterface $em,
        AuthUserInfoService $authUserInfoService
    )
    {
        parent::__construct($dataTableFactory, $router, $em);
        $this->authUserInfoService = $authUserInfoService;
    }

    /**
     * Таблица диагнозов в админке
     *
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array|null $filters
     * @param array $options
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
        $patientInfoService = new PatientInfoService();
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'fio', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('fio'),
                    'field' => 'u.lastName',
                    'render' => function (string $data, Patient $patient) {
                        return
                            '<a href="' . $this->router->generate(
                                'doctor_medical_history',
                                [
                                    'id' => $patient->getId()
                                ]
                            ) . '">' . $this->authUserInfoService->getFIO($patient->getAuthUser()) . '</a>';
                    },
                    'orderable' => true,
                    'orderField' => 'u.lastName',
                ]
            )
            ->add(
                'firstName', TextColumn::class, [
                    'field' => 'upper(u.firstName)',
                    'searchable' => true,
                    'visible' => false
                ]
            )
            ->add(
                'lastName', TextColumn::class, [
                    'field' => 'upper(u.lastName)',
                    'searchable' => true,
                    'visible' => false
                ]
            )
            ->add(
                'patronymicName', TextColumn::class, [
                    'field' => 'upper(u.patronymicName)',
                    'searchable' => true,
                    'visible' => false
                ]
            )
            ->add(
                'dateOfBirth', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('dateOfBirth'),
                    'data' => function (Patient $patient) {
                        return $patient->getDateBirth();
                    },
                    'searchable' => true,
                    'orderable' => true,
                    'orderField' => 'p.dateBirth',
                    'format' => 'd.m.Y'
                ]
            )
            ->add(
                'age', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('age'),
                    'data' => function ($value) use ($patientInfoService) {
                        return $patientInfoService->getAge($value);
                    },
                    'orderable' => true,
                    'orderField' => 'p.dateBirth',
                ]
            )
            ->add(
                'hospital', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('hospital'),
                    'field' => 'h.name',
                    'render' => function (string $data, Patient $patient) {
                        return $patient ? $patient->getHospital()->getName() : '';
                    },
                    'orderable' => true,
                    'orderField' => 'h.name',
                ]
            )
            ->add(
                'city', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('city'),
                    'field' => 'h.name',
                    'render' => function (string $data, Patient $patient) use ($listTemplateItem) {
                        return $patient->getCity()
                            ? $patient->getCity()->getName()
                            : $listTemplateItem->getContentValue('empty');
                    },
                    'orderable' => true,
                    'orderField' => 'h.name',
                ]
            )
            ->add(
                'status', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('status'),
                    'render' => function (string $data, Patient $patient) {
                        $patientTestingsWithNoResults = $this->entityManager
                            ->getRepository(PatientTesting::class)
                            ->getNoResultsTestingsForPatientsList($patient);
                        $patientTestingsNoProcessedTestings = $this->entityManager
                            ->getRepository(PatientTesting::class)
                            ->getNoProcessedTestingsForPatientsList($patient);
                        $patientOpenedPrescriptions = $this->entityManager
                            ->getRepository(Prescription::class)
                            ->getOpenedPrescriptionsForPatientList($patient);

                        $result = "";
                        if (!empty($patientTestingsWithNoResults)) {
                            $result .= $this->getLink('Нет анализов',
                                $patient->getId(),
                                'doctor_medical_history'
                            );
                        }
                        if (!empty($patientTestingsNoProcessedTestings)) {
                            $result .= $this->generateResultStringIfNotEmpty($patientTestingsWithNoResults);
                            $result .= $this->getLink('Обработать анализы',
                                $patient->getId(),
                                'doctor_medical_history'
                            );
                        }
                        if (!empty($patientOpenedPrescriptions)) {
                            $result .= $this->generateResultStringIfNotEmpty($patientTestingsNoProcessedTestings);
                            $result .= $this->generateResultStringIfNotEmpty($patientTestingsWithNoResults);
                            $result .= $this->getLink('Закрыть назначения',
                                $patient->getId(),
                                'doctor_medical_history'
                            );
                        }
                        return $result;
                    },
                ]
            );

        if ($filters[AppAbstractController::FILTER_LABELS['HOSPITAL']] !== "") {
            $hospital = $filters[AppAbstractController::FILTER_LABELS['HOSPITAL']];
        } elseif ($options) {
            $hospital = $options['hospital'];
        } else {
            $hospital = "";
        }


        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Patient::class,
                    'query' => function (QueryBuilder $builder) use ($hospital) {
                        $builder
                            ->select('p')
                            ->from(Patient::class, 'p')
                            ->leftJoin('p.AuthUser', 'u')
                            ->leftJoin('p.hospital', 'h')
                            ->andWhere('u.enabled = :val')
                            ->setParameter('val', true);
                        if ($hospital) {
                            $builder
                                ->andWhere('p.hospital = :valHospital')
                                ->setParameter('valHospital', $hospital);
                        }
                    },
                    'criteria' => $this->criteriaSearch(),
                ]
            );
    }

    /**
     * @param $patientTestings
     * @return string
     */
    private function generateResultStringIfNotEmpty($patientTestings): string
    {
        return !empty($patientTestings) ? "<hr>" : "";
    }
}