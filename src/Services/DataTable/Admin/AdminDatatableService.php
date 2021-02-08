<?php

namespace App\Services\DataTable\Admin;

use App\Entity\PatientTesting;
use App\Services\DataTable\DataTableService;
use App\Services\Template\TemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AdminDatatableService
 * содержит повторяющиеся методы для сервисов datatable админки
 *
 * @package App\Services\DataTable\Admin
 */
abstract class AdminDatatableService extends DataTableService
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
    public function __construct(DataTableFactory $dataTableFactory, UrlGeneratorInterface $router, EntityManagerInterface $entityManager = null)
    {
        parent::__construct($dataTableFactory);
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    /**
     * Добавляет поле с порядковым номером
     *
     * @return DataTable
     */
    protected function addSerialNumber(): DataTable
    {
        return $this->dataTable
            ->add(
                'serialNumber', TextColumn::class, [
                    'label' => '№',
                    'data' => '1'
                ]
            );
    }

    /**
     * Добавляет поле с флагом ограничения использования
     *
     * @param TemplateItem $templateItem
     * @param string $prefix
     *
     * @return DataTable
     * @throws Exception
     */
    protected function addEnabled(TemplateItem $templateItem, string $prefix = ''): DataTable
    {
        $addParameters = [
            'trueValue' => $templateItem->getContentValue('trueValue'),
            'falseValue' => $templateItem->getContentValue('falseValue'),
            'label' => $templateItem->getContentValue('enabled'),
            'searchable' => false,
        ];
        if ($prefix) {
            $addParameters['field'] = $prefix . '.enabled';
        }
        return $this->dataTable
            ->add('enabled', BoolColumn::class, $addParameters);
    }

    /**
     * Добавляет поле с операциями
     *
     * @param Closure $renderOperationsFunction
     *
     * @param TemplateItem $templateItem
     * @return DataTable
     * @throws Exception
     */
    protected function addOperations(Closure $renderOperationsFunction, TemplateItem $templateItem): DataTable
    {
        return $this->dataTable
            ->add(
                'operations', TextColumn::class, [
                    'label' => $templateItem->getContentValue('operations'),
                    'className' => 'dataTableOperations',
                    'render' => $renderOperationsFunction,
                    'field' => 'e.id',
                    'searchable' => false
                ]
            );
    }

    /**
     * Добавляет поле с операциями c возможностью добавления параметров
     * Клоушура используется для render, чтобы можно было задать сколь угодно много динамических параметров
     * Клоушура задается в DataTable сервисе
     *
     * @param TemplateItem $templateItem
     * @param Closure $renderFunction
     * @return DataTable
     * @throws Exception
     */
    protected function addOperationsWithParameters(
        TemplateItem $templateItem,
        Closure $renderFunction
    ): DataTable
    {
        return $this->dataTable
            ->add(
                'operations', TextColumn::class, [
                    'label' => $templateItem->getContentValue('operations'),
                    'className' => 'dataTableOperations',
                    'render' => $renderFunction,
                    'field' => 'e.id',
                    'searchable' => false
                ]
            );
    }

    /**
     * Get link
     *
     * @param string $value
     * @param int $id
     * @param string $route
     *
     * @return string
     */
    protected function getLink(string $value, int $id, string $route): string
    {
        return '<a href="' . $this->router->generate($route, ['id' => $id]) . '">' . $value . '</a>';
    }

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
                                    : '<span class="redRow">'.$patientTesting->getAnalysisGroup()->getName().'</span>'
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
        } {
        return true;
    }
    }
}