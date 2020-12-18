<?php


namespace App\Services\DataTable\DoctorOffice;

use App\Entity\Hospital;
use App\Entity\Patient;
use App\Services\DataTable\Admin\AdminDatatableService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class DataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class NewDataTableService extends AdminDatatableService
{
    /**
     * Таблица диагнозов в админке
     *
     * @param Hospital|null $hospital
     *
     * @return DataTable
     */
    public function getTable(?Hospital $hospital = null): DataTable
    {
        $patientInfoService = new PatientInfoService();
        $this->addSerialNumber();
        return $this->dataTable
            ->add(
                'fio', TextColumn::class, [
                    'label' => 'ФИО',
                    'data' => function ($value) {
                        return (new AuthUserInfoService())->getFIO($value->getAuthUser(), true);
                    },
                    'className'=>'mode strong'
                ]
            )
            ->add(
                'hospital', TextColumn::class, [
                    'label' => 'Больница',
                    'field' => 'h.name'
                ]
            )
            ->add(
                'age', TextColumn::class, [
                    'label' => 'Возраст',
                    'className' => 'vertical',
                    'data' => function ($value) use ($patientInfoService) {
                        return $patientInfoService->getAge($value);
                    }
                ]
            )
            ->add(
                'diagnoses', TextColumn::class, [
                    'label' => 'Диагнозы',
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
//            ->add(
//                'unprocessedTestings', TextColumn::class, [
//                    'label' => 'Показатели',
//                    'data' => function ($value) use ($patientInfoService) {
//                        $unprocessedTestings = '';
//                        /** @var PatientTesting $testing */
//                        foreach ($patientInfoService->getUnprocessedTestings($value) as $testing) {
//                            $unprocessedTestings .= $testing->getAnalysisGroup()->getName().'<br/>';
//                        }
//                        return $unprocessedTestings;
//                    },
//                    'raw' => true
//                ]
//            )
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
                ]
            );
    }
}