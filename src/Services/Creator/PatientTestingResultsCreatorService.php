<?php

namespace App\Services\Creator;

use App\Entity\Analysis;
use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PatientTestingResultsCreatorService
 * @package App\Services\Creator
 */
class PatientTestingResultsCreatorService
{
    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /**
     * PatientTestingResultsCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Persist testing results for testing
     * @param PatientTesting $patientTesting
     */
    public function persistTestingResultsForTesting(PatientTesting $patientTesting): void
    {
        foreach ($patientTesting->getAnalysisGroup()->getAnalyses() as $analysis) {
            if ($analysis->getEnabled()) {
                $this->entityManager->persist($this->createPatientTestingResult($patientTesting, $analysis));
            }
        }
    }

    /**
     * Create patient testing result entity object
     * @param PatientTesting $patientTesting
     * @param Analysis $analysis
     * @return PatientTestingResult
     */
    public function createPatientTestingResult(PatientTesting $patientTesting, Analysis $analysis): PatientTestingResult
    {
        return (new PatientTestingResult())
            ->setPatientTesting($patientTesting)
            ->setAnalysis($analysis)
            ->setEnabled(false);
    }
}