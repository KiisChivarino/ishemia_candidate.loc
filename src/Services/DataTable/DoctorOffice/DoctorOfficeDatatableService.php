<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Entity\PatientTesting;
use App\Services\DataTable\DataTableService;
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
     * @throws \Exception
     */
    protected function generateTableForPatientTestingsInDoctorOffice(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem
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
                                ? $this->isPatientTestingInRangeOfReferentValues($patientTesting)
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
                    'format' => 'd.m.Y H:i'
                ]
            );
        $this->addOperationsWithParameters(
            $listTemplateItem,
            function (string $data, PatientTesting $patientTesting) use ($renderOperationsFunction) {
                return
                    $renderOperationsFunction(
                        (string)$patientTesting->getMedicalHistory()->getPatient()->getId(),
                        ['patientTesting' => $patientTesting->getId()]
                    );
            }
        );
    }

    /**
     * Checks if patient analysis is in range of referent values
     * If analysis doesnt have referent values returns true
     * @param $patientTesting
     * @return bool
     */
    private function isPatientTestingInRangeOfReferentValues($patientTesting): bool
    {
        foreach ($patientTesting->getPatientTestingResults() as $result) {
            if (!is_null($result->getResult())
                && !is_null($result->getAnalysisRate())
                && !is_null($result->getAnalysisRate()->getRateMax())
                && !is_null($result->getAnalysisRate()->getRateMin())
            ) {
                if (
                    $result->getAnalysisRate()->getRateMax() >= $result->getResult()
                    && $result->getResult() >= $result->getAnalysisRate()->getRateMin()
                ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
        return true;
    }
}