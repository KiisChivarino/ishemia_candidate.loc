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
     * Подготовить результаты анализов для обследования
     *
     * @param PatientTesting $patientTesting
     *
     * @throws ORMException
     */
    public function persistTestingResultsForTesting(PatientTesting $patientTesting)
    {
        foreach ($patientTesting->getAnalysisGroup()->getAnalyses() as $analysis) {
            if ($analysis->getEnabled()) {
                $analysisTestingResult = new PatientTestingResult();
                $analysisTestingResult->setPatientTesting($patientTesting);
                $analysisTestingResult->setAnalysis($analysis);
                $analysisTestingResult->setEnabled(false);
                $this->_em->persist($analysisTestingResult);
            }
        }
    }
}
