<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Entity\PatientTesting;
use App\Services\DataTable\DataTableService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class DoctorOfficeDatatableService
 * содержит повторяющиеся методы для сервисов datatable доктор оффиса
 *
 * @package App\Services\DataTable\Admin
 */
abstract class DoctorOfficeDatatableService extends DataTableService
{
    /** @var UrlGeneratorInterface|null $router */
    protected $router;

    /** @var EntityManagerInterface|null $entityManager */
    protected $entityManager;

    /**
     * AdminDatatableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     * @param UrlGeneratorInterface|null $router
     * @param EntityManagerInterface|null $entityManager
     */
    public function __construct(
        DataTableFactory $dataTableFactory,
        UrlGeneratorInterface $router,
        EntityManagerInterface $entityManager = null
    )
    {
        parent::__construct($dataTableFactory);
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    /**
     * Generates table for patient testings
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param string|null $route
     * @throws \Exception
     */
    protected function generateTableForPatientTestingsInDoctorOffice(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem,
        string $route = null
    )
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'analysisGroup', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysisGroup'),
                    'field' => 'aG.name',
                    'render' => function (string $data, PatientTesting $patientTesting) {
                        return
                            $patientTesting
                                ? PatientTestingInfoService::isPatientTestingInRangeOfReferentValues($patientTesting)
                                ? $patientTesting->getAnalysisGroup()->getName()
                                : '<span class="redRow">' . $patientTesting->getAnalysisGroup()->getName() . '</span>'
                                : '';
                    },
                    'orderable' => true,
                    'orderField' => 'aG.name',
                ]
            )
            ->add(
                'analysisDate', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysisDate'),
                    'searchable' => false,
                    'format' => 'd.m.Y H:i',
                    'nullValue' => $listTemplateItem->getContentValue('empty')
                ]
            );
        $this->addOperationsWithParameters(
            $listTemplateItem,
            function (int $patientTestingId, PatientTesting $patientTesting) use ($renderOperationsFunction, $route) {
                return
                    $renderOperationsFunction(
                        (string)$patientTesting->getMedicalHistory()->getPatient()->getId(),
                        $patientTesting,
                        $route,
                        [
                            'patientTesting' => $patientTestingId
                        ]
                    );
            }
        );
    }
}