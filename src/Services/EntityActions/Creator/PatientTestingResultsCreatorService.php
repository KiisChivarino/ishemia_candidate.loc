<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\Analysis;
use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use App\Services\EntityActions\Core\AbstractCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PatientTestingResultsCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PatientTestingResultsCreatorService extends AbstractCreatorService
{
    /** @var string Patient testing entity actions option */
    public const PATIENT_TESTING_OPTION = 'patientTesting';

    /** @var string Analysis entity actions option */
    public const ANALYSIS_OPTION = 'analysis';

    /**
     * PatientTestingResultsCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($entityManager, PatientTestingResult::class);
    }

    protected function prepare(): void
    {
        /** @var PatientTestingResult $patientTestingResult */
        $patientTestingResult = $this->getEntity();
        $patientTestingResult
            ->setPatientTesting($this->options[self::PATIENT_TESTING_OPTION])
            ->setAnalysis($this->options[self::ANALYSIS_OPTION]);
    }

    public function configureOptions(): void
    {
        $this->addOptionCheck(PatientTesting::class, self::PATIENT_TESTING_OPTION);
        $this->addOptionCheck(Analysis::class, self::ANALYSIS_OPTION);
    }
}