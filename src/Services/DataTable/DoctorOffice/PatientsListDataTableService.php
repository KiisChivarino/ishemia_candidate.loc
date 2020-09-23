<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Services\DataTable\Admin\AdminDatatableService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;

/**
 * Class DataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class PatientsListDataTableService extends AdminDatatableService
{
    /** @var DataTableFactory $dataTableFactory */
    protected $dataTableFactory;

    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * DataTableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     * @param EntityManagerInterface $em
     */
    public function __construct(DataTableFactory $dataTableFactory, EntityManagerInterface $em)
    {
        parent::__construct($dataTableFactory);
        $this->dataTableFactory = $dataTableFactory;
        $this->em = $em;
    }

    /**
     * Таблица диагнозов в админке
     *
     * @param ListTemplateItem $listTemplateItem
     * @param array $filters
     *
     * @return DataTable
     */
    public function getTable(ListTemplateItem $listTemplateItem, array $filters): DataTable
    {
        $patientInfoService = new PatientInfoService();
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'fio', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('fio'),
                    'data' => function ($value) {
                        return (new AuthUserInfoService())->getFIO($value->getAuthUser(), true);
                    }
                ]
            )
            ->add(
                'age', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('age'),
                    'data' => function ($value) use ($patientInfoService) {
                        return $patientInfoService->getAge($value);
                    }
                ]
            )
//            ->add(
//                'gestationAge', TextColumn::class, [
//                    'label' => $listTemplateItem->getContentValue('gestationAge'),
//                    'data' => function ($value) use ($patientInfoService) {
//                        return $patientInfoService->getGestationWeeks($value->getDateStartOfTreatment());
//                    }
//                ]
//            )
            ->add(
                'diagnoses', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('diagnoses'),
                    'data' => function ($value) {
                        $diagnoses = '';
                        foreach ($value->getDiagnosis() as $diagnosis) {
                            $diagnoses .= $diagnosis->getName().'<br/>';
                        }
                        return $diagnoses;
                    },
                    'raw' => true
                ]
            )
            ->add(
                'unprocessedTestings', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('unprocessedTestings'),
                    'data' => function ($value) use ($patientInfoService) {
                        $unprocessedTestings = '';
                        /** @var PatientTesting $testing */
                        foreach ($patientInfoService->getUnprocessedTestings($value) as $testing) {
                            $unprocessedTestings .= $testing->getAnalysisGroup()->getName().'<br/>';
                        }
                        return $unprocessedTestings;
                    },
                    'raw' => true
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