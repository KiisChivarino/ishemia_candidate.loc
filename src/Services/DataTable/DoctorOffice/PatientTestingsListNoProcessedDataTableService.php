<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\PatientTesting;
use App\Services\DataTable\Admin\AdminDatatableService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PatientTestingsListNoProcessedDataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class PatientTestingsListNoProcessedDataTableService extends AdminDatatableService
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
    public function __construct(DataTableFactory $dataTableFactory, UrlGeneratorInterface $router, EntityManagerInterface $em, AuthUserInfoService $authUserInfoService)
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
     * @param array $options
     * @return DataTable
     * @throws Exception
     */
    public function getTable(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem,
        array $filters,
        array $options
    ): DataTable
    {
        $this->generateTableForPatientTestingsInDoctorOffice($renderOperationsFunction, $listTemplateItem);

        $analysisGroup = $filters[AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP']];
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PatientTesting::class,
                    'query' => function (QueryBuilder $builder) use ($analysisGroup, $options) {
                        $builder
                            ->select('pT')
                            ->from(PatientTesting::class, 'pT')
                            ->leftJoin('pT.medicalHistory', 'mH')
                            ->leftJoin('mH.patient', 'p')
                            ->leftJoin('p.AuthUser', 'u')
                            ->leftJoin('pT.analysisGroup', 'aG')
                            ->andWhere('u.enabled = :val')
                            ->andWhere('p.id = :patientId')
                            ->andWhere('pT.isProcessedByStaff = false')
                            ->andWhere('pT.hasResult = true')
                            ->setParameter('patientId', $options['patientId'])
                            ->setParameter('val', true);
                        if ($analysisGroup) {
                            $builder
                                ->andWhere('pT.analysisGroup = :valAnalysisGroup')
                                ->setParameter('valAnalysisGroup', $analysisGroup);
                        }
                    },
                ]
            );
    }
}