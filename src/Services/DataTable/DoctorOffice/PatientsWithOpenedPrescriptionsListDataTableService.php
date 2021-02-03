<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Services\DataTable\Admin\AdminDatatableService;
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
class PatientsWithOpenedPrescriptionsListDataTableService extends AdminDatatableService
{
    private $authUserInfoService;

    /**
     * DataTableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     * @param UrlGeneratorInterface $router
     * @param EntityManagerInterface $em
     * @param AuthUserInfoService $authUserInfoService
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
                            $patient
                                ? $this->getLink(
                                $this->authUserInfoService->getFIO($patient->getAuthUser()),
                                $patient->getId(),
                                'doctor_medical_history'
                            )
                                : '';
                    },
                    'orderable' => true,
                    'orderField' => 'u.lastName',
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
                    'render' => function (string $data, Patient $patient) {
                        return $patient ? $patient->getCity()->getName() : '';
                    },
                    'orderable' => true,
                    'orderField' => 'h.name',
                ]
            )
        ;
        $hospital = $filters[AppAbstractController::FILTER_LABELS['HOSPITAL']] !== ""
            ? $filters[AppAbstractController::FILTER_LABELS['HOSPITAL']]
            : ($options
                ? $options['hospital']
                : "");
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Patient::class,
                    'query' => function (QueryBuilder $builder) use ($hospital) {
                        $builder
                            ->select('p')
                            ->from(Patient::class, 'p')
                            ->andWhere('p.id IN (:patients)')
                            ->setParameter(
                                'patients',
                                $this->entityManager
                                    ->getRepository(Prescription::class)->getOpenedPrescriptions()
                            )
                        ;

                        if ($hospital) {
                            $builder
                                ->andWhere('p.hospital = :valHospital')
                                ->setParameter('valHospital', $hospital);
                        }
                    },
                ]
            );
    }
}