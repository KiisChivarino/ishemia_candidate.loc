<?php

namespace App\Repository;

use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientTesting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientTesting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientTesting[]    findAll()
 * @method PatientTesting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientTestingRepository extends AppRepository
{
    /**
     * PatientTestingRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientTesting::class);
    }

    /**
     * Возвращает тесты для пациента по плану
     *
     * @param PatientTesting $patientTesting
     *
     * @return void
     * @throws ORMException
     */
//    public function persistPatientTests(MedicalHistory $medicalHistory, array $planTesting)
//    {
//        $patientTests = [];
//        /** @var PlanTesting $test */
//        foreach ($planTesting as $test) {
//            $patientTest = new PatientTesting();
//            $patientTest->setMedicalHistory($medicalHistory);
//            $patientTest->setAnalysisGroup($test->getAnalysisGroup());
//            $patientTest->setProcessed(false);
//            $patientTest->setEnabled(true);
//            $patientTest->setAnalysisDate(null);
//            $this->_em->persist($test);
//            $this->_em->getRepository(PatientTestingResult::class)->persistTestingResultsForTesting($patientTest);
//        }
//        return $patientTests;
//    }

    /**
     * Adds patient testing results for patient testing
     *
     * @param PatientTesting $patientTesting
     *
     * @throws ORMException
     */
    public function persistPatientTestingResults(PatientTesting $patientTesting): void
    {
        $analyses = $patientTesting->getAnalysisGroup()->getAnalyses();
        foreach ($analyses as $analysis) {
            if ($analysis->getEnabled()) {
                $this->_em->persist(
                    (new PatientTestingResult())
                        ->setPatientTesting($patientTesting)
                        ->setAnalysis($analysis)
                        ->setEnabled(false)
                );
            }
        }
    }
}
