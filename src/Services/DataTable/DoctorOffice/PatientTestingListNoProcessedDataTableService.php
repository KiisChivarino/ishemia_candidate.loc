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
class PatientTestingListNoProcessedDataTableService extends AdminDatatableService
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
                        $this->entityManager->getRepository(PatientTesting::class)
                            ->patientTestingsNoProcessedForDatatable($builder, $options['patientId'], $analysisGroup);
                    },
                ]
            );
    }
}