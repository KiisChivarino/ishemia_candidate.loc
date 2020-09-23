<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use App\Entity\PlanTesting;
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
     * @param MedicalHistory $medicalHistory
     * @param array $planTesting
     *
     * @return array|bool
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
//            $patientTest->setGestationalMinAge($test->getGestationalMinAge());
//            $patientTest->setGestationalMaxAge($test->getGestationalMaxAge());
//            $patientTest->setProcessed(false);
//            $patientTest->setEnabled(true);
//            $patientTest->setAnalysisDate(null);
//            $this->_em->persist($test);
//            $this->_em->getRepository(PatientTestingResult::class)->persistTestingResultsForTesting($patientTest);
//        }
//        return $patientTests;
//    }
}
