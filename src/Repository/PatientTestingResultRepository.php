<?php

namespace App\Repository;

use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientTestingResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientTestingResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientTestingResult[]    findAll()
 * @method PatientTestingResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientTestingResultRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientTestingResult::class);
    }

    /**
     * Возвращает анализы, подходящие для добавления по ним значений теста.
     * Отсекает выключенные анализы, а также анализы, для которых нет нормальных значений
     * с периодом, попавшим в сроки тестирования
     *
     * @param PatientTesting $patientTesting
     *
     * @return array
     */
//    public function getAnalyzes(PatientTesting $patientTesting): array
//    {
//        $analyzesResult = [];
//        foreach ($patientTesting->getAnalysisGroup()->getAnalyses() as $analysis) {
//            $maxAnalysisRateGestationMaxAge = $this->_em->getRepository(AnalysisRate::class)->getMaxGestationMaxAge($analysis);
//            $minAnalysisRateGestationMinAge = $this->_em->getRepository(AnalysisRate::class)->getMinGestationMinAge($analysis);
//            if ($analysis->getEnabled()
//                && !($patientTesting->getGestationalMinAge() > $maxAnalysisRateGestationMaxAge
//                    || $patientTesting->getGestationalMaxAge() < $minAnalysisRateGestationMinAge)
//            ) {
//                $analyzesResult[] = $analysis;
//            }
//        }
//        return $analyzesResult;
//    }

    /**
     * Подготовить результаты анализов для обследования
     *
     * @param PatientTesting $patientTesting
     *
     * @throws ORMException
     */
//    public function persistTestingResultsForTesting(PatientTesting $patientTesting)
//    {
//        foreach ($this->getAnalyzes($patientTesting) as $analysis) {
//            $analysisTestingResult = new PatientTestingResult();
//            $analysisTestingResult->setPatientTesting($patientTesting);
//            $analysisTestingResult->setAnalysis($analysis);
//            $analysisTestingResult->setEnabled(false);
//            $this->_em->persist($analysisTestingResult);
//        }
//    }
}
