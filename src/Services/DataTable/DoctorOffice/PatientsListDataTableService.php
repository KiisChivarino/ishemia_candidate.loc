<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Services\DataTable\Admin\AdminDatatableService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
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
class PatientsListDataTableService extends AdminDatatableService
{
    /** @var AuthUserInfoService $authUserInfoService */
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
        AuthUserInfoService $authUserInfoService,
        EntityManagerInterface $em
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
     * @param array $filters
     *
     * @return DataTable
     * @throws Exception
     */
    public function getTable(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem,
        array $filters): DataTable
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
                            '<a href="' . $this->router->generate('doctor_medical_history', [
                                    'id' => $patient->getId(),
                                    'medical_history' =>
                                        $this
                                            ->entityManager
                                            ->getRepository(MedicalHistory::class)
                                            ->getCurrentMedicalHistory($patient)
                                            ->getId()
                                ]
                            ) . '">' . $this->authUserInfoService->getFIO($patient->getAuthUser()) . '</a>';
                    },
                    'orderable' => true,
                    'orderField' => 'u.lastName',
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
            );
        $hospital = $filters[AppAbstractController::FILTER_LABELS['HOSPITAL']];
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Patient::class,
                    'query' => function (QueryBuilder $builder) use ($hospital) {
                        $builder
                            ->select('p')
                            ->from(Patient::class, 'p')
                            ->leftJoin('p.AuthUser', 'u')
                            ->andWhere('u.enabled = :val')
                            ->setParameter('val', true);
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