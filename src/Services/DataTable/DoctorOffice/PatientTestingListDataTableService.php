<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\PatientTesting;
use App\Repository\PatientTestingDatatableRepository;
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
 * Class PatientTestingsListDataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class PatientTestingListDataTableService extends DoctorOfficeDatatableService
{
    /*** @var AuthUserInfoService */
    private $authUserInfoService;

    /** @var PatientTestingDatatableRepository */
    private $patientTestingDatatableRepository;

    /**
     * DataTableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     * @param UrlGeneratorInterface $router
     * @param EntityManagerInterface $em
     * @param AuthUserInfoService $authUserInfoService
     * @param PatientTestingDatatableRepository $patientTestingDatatableRepository
     */
    public function __construct(
        DataTableFactory $dataTableFactory,
        UrlGeneratorInterface $router,
        EntityManagerInterface $em,
        AuthUserInfoService $authUserInfoService,
        PatientTestingDatatableRepository $patientTestingDatatableRepository
    )
    {
        parent::__construct($dataTableFactory, $router, $em);
        $this->authUserInfoService = $authUserInfoService;
        $this->patientTestingDatatableRepository = $patientTestingDatatableRepository;
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
        $this->generateTableForPatientTestingsInDoctorOffice(
            $renderOperationsFunction,
            $listTemplateItem,
            $options['route'] ?? null
        );
        $analysisGroup = $filters[AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP']];
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PatientTesting::class,
                    'query' => function (QueryBuilder $builder) use ($analysisGroup, $options) {
                        $this->patientTestingDatatableRepository
                            ->getPatientTestingsForDatatable($builder, $options['patientId'], $analysisGroup);
                    },
                    'criteria' => $this->criteriaSearch(),
                ]
            );
    }
}